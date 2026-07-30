<?php

declare(strict_types=1);

namespace Puntjes\Auth;

use Puntjes\Config;
use Puntjes\Exception\AuthenticationException;
use Puntjes\Exception\PuntjesException;
use Puntjes\Exception\TransportException;
use Puntjes\Http\ErrorMapper;
use Puntjes\Http\HttpClient;
use Puntjes\Http\Response;

/**
 * Obtains tokens through the OAuth2 `client_credentials` grant at `POST /oauth/token`,
 * caching each one in a {@see TokenStore} until shortly before it expires.
 *
 * Puntjes issues no other grant — there is no user to redirect and no refresh token;
 * re-granting is simply another POST with the same credentials.
 */
final class ClientCredentialsProvider implements TokenProvider
{
    private readonly string $cacheKey;

    public function __construct(
        private readonly Config $config,
        private readonly HttpClient $http,
        private readonly TokenStore $store,
    ) {
        // Scoped per credential pair so two vendors configured in one process — or one
        // vendor mid secret-rotation — never read each other's token out of a shared cache.
        $this->cacheKey = 'puntjes:token:'.$config->credentialFingerprint();
    }

    public function token(bool $forceRefresh = false): AccessToken
    {
        if ($forceRefresh) {
            $this->store->forget($this->cacheKey);
        } else {
            $cached = $this->store->get($this->cacheKey);

            if ($cached !== null && ! $cached->isExpired(Config::EXPIRY_LEEWAY)) {
                return $cached;
            }
        }

        $token = $this->grant();

        // Expire the cache entry a little before the token itself dies, so a value
        // read at the very end of its life is still usable for the request it serves.
        $this->store->put($this->cacheKey, $token, max(1, $token->expiresIn() - Config::EXPIRY_LEEWAY));

        return $token;
    }

    /** Discard the cached token — used by the transport on a 401. */
    public function forget(): void
    {
        $this->store->forget($this->cacheKey);
    }

    private function grant(): AccessToken
    {
        $issuedAt = time();

        $response = $this->http->send(
            method: 'POST',
            url: $this->config->tokenUrl(),
            headers: [
                // Form encoding, not JSON: this is league/oauth2-server's endpoint and
                // RFC 6749 §4.4.2 mandates application/x-www-form-urlencoded here. The
                // rest of the API is JSON.
                'Content-Type' => 'application/x-www-form-urlencoded',
                'Accept' => 'application/json',
            ],
            body: http_build_query([
                'grant_type' => 'client_credentials',
                'client_id' => $this->config->clientId,
                'client_secret' => $this->config->clientSecret,
            ], '', '&', PHP_QUERY_RFC1738),
        );

        if (! $response->isSuccessful()) {
            throw $this->grantFailure($response);
        }

        $token = AccessToken::fromResponse($response->json(), $issuedAt);

        if ($token->accessToken === '') {
            throw new TransportException(sprintf(
                'The Puntjes token endpoint returned no access_token: %s',
                $response->bodyExcerpt(),
            ));
        }

        return $token;
    }

    /**
     * Only a 400/401 from the token endpoint means the credentials were rejected —
     * and only those answer in OAuth2's flat `{"error": "invalid_client",
     * "error_description": …}` shape (league/oauth2-server, not the API envelope).
     *
     * Everything else — a 429 from the token route's throttle, a 5xx, a proxy error
     * page — is an infrastructure problem, not a credential one. Those are mapped
     * exactly like any API response, so `ServerException` / `RateLimitException` keep
     * their meaning and the transport's retry policy applies to them. Typing a 502 as
     * an authentication failure would send an operator hunting for a credentials bug
     * during an outage.
     *
     * Credentials are never included in any message.
     */
    private function grantFailure(Response $response): PuntjesException
    {
        if ($response->status === 400 || $response->status === 401) {
            $body = $response->tryJson() ?? [];

            $code = is_string($body['error'] ?? null) ? $body['error'] : 'invalid_client';
            $description = is_string($body['error_description'] ?? null)
                ? $body['error_description']
                : 'The Puntjes API rejected the client credentials.';

            return new AuthenticationException(
                message: sprintf(
                    'OAuth token request failed (HTTP %d): %s. Check PUNTJES_CLIENT_ID / PUNTJES_CLIENT_SECRET, and that the API client has not been revoked.',
                    $response->status,
                    $description,
                ),
                status: $response->status,
                errorCode: strtoupper($code),
                requestId: $response->header('x-request-id'),
                details: null,
                body: $body,
            );
        }

        try {
            return ErrorMapper::toException($response);
        } catch (TransportException $e) {
            // Non-JSON below 500 — a WAF or login page where the token endpoint
            // should be. Not retryable, but not an auth failure either.
            return $e;
        }
    }
}
