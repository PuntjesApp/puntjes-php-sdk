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
 *     `/redemptions/{code}/verify`, `/vouchers/{code}/verify` and `/products/{sku}/reward`
 *     when the caller gives a key, where a unique index plus a savepoint-protected claim
 *     make a replay return the original
 *     record instead of moving points twice or spending a bon twice.
 *   - Every other POST — never. `/customers`, `/products`, `/products/batch`,
 *     and the verify and reward calls above without a key have no
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

    /**
     * Sends a request built elsewhere, the generated client's, under the same token, retry and error rules.
     *
     * @param  array<string, string>  $headers
     * @param  bool  $authenticate  False for an operation the spec makes public: no token, and a 401 is not re-granted
     *
     * @throws ApiException|TransportException
     */
    public function sendRaw(string $method, string $url, array $headers, ?string $body, bool $authenticate = true): Response
    {
        $method = strtoupper($method);
        $decoded = $body === null || $body === '' ? null : json_decode($body, true);

        return $this->send($method, $url, $body === '' ? null : $body, self::isRetryable($method, is_array($decoded) ? $decoded : null), $headers, $authenticate);
    }

    /** @param array<string, string> $headers */
    private function send(string $method, string $url, ?string $encodedBody, bool $retryable, array $headers, bool $authenticate): Response
    {
        $attempt = 0;
        $tokenRefreshed = false;
        $forceRefresh = false;

        while (true) {
            try {
                $token = $authenticate ? $this->tokens->token(forceRefresh: $forceRefresh) : null;
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
                ['Accept' => 'application/json'],
                $token === null ? [] : ['Authorization' => $token->authorizationHeader()],
                $encodedBody === null || self::hasHeader($headers, 'Content-Type') ? [] : ['Content-Type' => 'application/json'],
                $headers,
            );

            try {
                $response = $this->http->send($method, $url, [], $requestHeaders, $encodedBody);
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
            if ($response->status === 401 && $authenticate && ! $tokenRefreshed) {
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

    /** @param array<string, string> $headers */
    private static function hasHeader(array $headers, string $name): bool
    {
        foreach (array_keys($headers) as $key) {
            if (strcasecmp((string) $key, $name) === 0) {
                return true;
            }
        }

        return false;
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
