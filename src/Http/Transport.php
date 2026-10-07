<?php

declare(strict_types=1);

namespace Puntjes\Http;

use Puntjes\Auth\TokenProvider;
use Puntjes\Config;
use Puntjes\Exception\ApiException;
use Puntjes\Exception\AuthenticationException;
use Puntjes\Exception\PlanLimitExceededException;
use Puntjes\Exception\RateLimitException;
use Puntjes\Exception\ServerException;
use Puntjes\Exception\TransportException;

/**
 * The authenticated request pipeline: attach a token, send, retry what is safe to
 * retry, and translate failures into typed exceptions.
 *
 * ## What gets retried
 *
 * Retrying a request that already changed something is how integrations double-charge
 * customers, so the rule is conservative and explicit:
 *
 *   - GET / HEAD — always. Reads have no effect.
 *   - PUT / PATCH / DELETE — always. Every one on this API is keyed by an external id
 *     and converges on the same state (`PUT /products/{sku}` is the catalogue-sync
 *     primitive; `DELETE` is a soft delete; `PATCH` sets named fields).
 *   - POST — only when the body carries an `idempotency_key`. That covers
 *     `/transactions`, `/redemptions`, `/customers/{id}/wallet/adjust`, and
 *     `/vouchers/{code}/verify` and `/products/{sku}/reward` when the caller gives a key, where a
 *     unique index plus a savepoint-protected claim make a replay return the original
 *     record instead of moving points twice or spending a bon twice.
 *   - Every other POST — never. `/customers`, `/products`, `/products/batch`,
 *     `/products/{sku}/reward` without a key and `/redemptions/{code}/verify` have no
 *     replay protection, so an auto-retry could duplicate a customer or burn a code.
 *
 * A retry only happens for failures that a later attempt could plausibly survive:
 * connection errors, 5xx, and 429 rate limiting. `PLAN_LIMIT_EXCEEDED` shares the 429
 * status but is a billing stop — retrying it just burns the remaining budget, so it is
 * excluded.
 *
 * ## Tokens
 *
 * A 401 triggers exactly one silent re-grant and replay, which is what makes a token
 * cached across processes safe: if it was revoked or expired early, the next call
 * transparently mints a new one. A second 401 is raised — the credentials are wrong.
 * The re-grant happens for every 401 code, `INVALID_CLIENT` included: a new token
 * does not fix that code on a current Puntjes, but a Puntjes from before
 * PuntjesApp/Puntjes#1084 sent it for an expired token too. It costs one grant.
 *
 * A grant that fails *transiently* (connection error, 5xx, 429 on the token route) is
 * retried under the same budget as the request itself — and, unlike the request, it is
 * retried regardless of method: the grant failing means the actual request was never
 * sent, so replaying it cannot duplicate a side effect. Only a credential rejection
 * ({@see AuthenticationException}) is never retried.
 */
final class Transport
{
    /** @var callable(float): void */
    private $sleeper;

    /**
     * @param  callable(float): void|null  $sleeper  Injectable for tests, so retry
     *                                               coverage does not spend real seconds.
     */
    public function __construct(
        private readonly Config $config,
        private readonly HttpClient $http,
        private readonly TokenProvider $tokens,
        ?callable $sleeper = null,
    ) {
        $this->sleeper = $sleeper ?? static function (float $seconds): void {
            usleep((int) round($seconds * 1_000_000));
        };
    }

    /** @param array<string, mixed> $query */
    public function get(string $path, array $query = []): Response
    {
        return $this->request('GET', $path, $query);
    }

    /**
     * @param  array<string, mixed>  $body
     * @param  array<string, mixed>  $query
     */
    public function post(string $path, array $body = [], array $query = []): Response
    {
        return $this->request('POST', $path, $query, $body);
    }

    /** @param array<string, mixed> $body */
    public function put(string $path, array $body = []): Response
    {
        return $this->request('PUT', $path, [], $body);
    }

    /** @param array<string, mixed> $body */
    public function patch(string $path, array $body = []): Response
    {
        return $this->request('PATCH', $path, [], $body);
    }

    public function delete(string $path): Response
    {
        return $this->request('DELETE', $path);
    }

    /**
     * Send an authenticated API request.
     *
     * @param  array<string, mixed>  $query
     * @param  array<string, mixed>|null  $body  Encoded as JSON when present.
     * @param  array<string, string>  $headers
     *
     * @throws ApiException|TransportException
     */
    public function request(
        string $method,
        string $path,
        array $query = [],
        ?array $body = null,
        array $headers = [],
    ): Response {
        $method = strtoupper($method);
        $url = $this->config->apiUrl($path);
        $encodedBody = $body === null ? null : json_encode($body, JSON_THROW_ON_ERROR);
        $retryable = self::isRetryable($method, $body);

        $attempt = 0;
        $tokenRefreshed = false;
        $forceRefresh = false;

        while (true) {
            try {
                $token = $this->tokens->token(forceRefresh: $forceRefresh);
                $forceRefresh = false;
            } catch (TransportException|ServerException|RateLimitException $e) {
                // The grant failed before the actual request was ever sent, so a retry
                // is side-effect-free no matter what the request is — replaying a
                // failed grant for POST /customers cannot duplicate a customer.
                // AuthenticationException deliberately falls through: rejected
                // credentials are not transient.
                if ($attempt < $this->config->maxRetries) {
                    $this->sleep($attempt, $e instanceof RateLimitException ? $e->retryAfter() : null);
                    $attempt++;

                    continue;
                }

                throw $e;
            }

            $requestHeaders = array_merge(
                $this->config->defaultHeaders,
                [
                    'Accept' => 'application/json',
                    'Authorization' => $token->authorizationHeader(),
                ],
                $encodedBody === null ? [] : ['Content-Type' => 'application/json'],
                $headers,
            );

            try {
                $response = $this->http->send($method, $url, $query, $requestHeaders, $encodedBody);
            } catch (TransportException $e) {
                // No response at all. Safe to replay only if the call itself is replay-safe:
                // the request may well have been processed before the connection dropped.
                if ($retryable && $attempt < $this->config->maxRetries) {
                    $this->sleep($attempt, null);
                    $attempt++;

                    continue;
                }

                throw $e;
            }

            if ($response->isSuccessful()) {
                return $response;
            }

            // One silent re-grant: the cached token was revoked, or the server restarted
            // with new Passport keys. Replaying is safe regardless of method — a 401 means
            // the request was rejected before reaching any business logic. The refresh
            // itself happens at the top of the loop, inside the guarded path, so a
            // transient failure during the re-grant is retried too.
            if ($response->status === 401 && ! $tokenRefreshed) {
                $tokenRefreshed = true;
                $forceRefresh = true;

                continue;
            }

            $exception = ErrorMapper::toException($response);

            if ($retryable && $attempt < $this->config->maxRetries && self::isRetryableFailure($exception)) {
                $this->sleep($attempt, $exception instanceof RateLimitException ? $exception->retryAfter() : null);
                $attempt++;

                continue;
            }

            throw $exception;
        }
    }

    /**
     * @param  array<string, mixed>|null  $body
     */
    private static function isRetryable(string $method, ?array $body): bool
    {
        if (in_array($method, ['GET', 'HEAD', 'PUT', 'PATCH', 'DELETE'], true)) {
            return true;
        }

        // A POST is replay-safe exactly when the server can recognise the replay.
        return $method === 'POST'
            && is_array($body)
            && isset($body['idempotency_key'])
            && $body['idempotency_key'] !== '';
    }

    private static function isRetryableFailure(ApiException $exception): bool
    {
        // Checked before the 429 case below: a plan stop and a rate limit share the
        // status, but only the rate limit clears by waiting.
        if ($exception instanceof PlanLimitExceededException) {
            return false;
        }

        return $exception instanceof ServerException || $exception instanceof RateLimitException;
    }

    /** Exponential backoff, unless the server said exactly how long to wait. */
    private function sleep(int $attempt, ?int $retryAfter): void
    {
        $delay = $retryAfter !== null
            ? (float) $retryAfter
            : $this->config->retryBaseDelay * (2 ** $attempt);

        ($this->sleeper)($delay);
    }
}
