<?php

declare(strict_types=1);

namespace Puntjes;

use Puntjes\Exception\ConfigurationException;

/**
 * Immutable client configuration.
 *
 * `$baseUrl` accepts the base URL exactly as the API documentation states it —
 * `https://puntjes.app/api/v1` — or the bare host, `https://puntjes.app`. Both are
 * equivalent here.
 *
 * Internally only the host is kept, because the two things this client talks to do
 * not share a prefix: the API lives under `/api/v1`, but the OAuth token endpoint is
 * at `/oauth/token`, off the root. A client that stored the documented base URL
 * verbatim and appended paths to it would look for the token at
 * `/api/v1/oauth/token` and never find it.
 */
final class Config
{
    public const API_PREFIX = '/api/v1';

    public const TOKEN_PATH = '/oauth/token';

    /** Refetch a token this many seconds before it actually expires. */
    public const EXPIRY_LEEWAY = 30;

    public readonly string $baseUrl;

    /**
     * @param  string  $clientId  OAuth client id from Puntjes → Settings → API clients.
     * @param  string  $clientSecret  The matching client secret. Never logged.
     * @param  string  $baseUrl  `https://puntjes.app/api/v1` or `https://puntjes.app` — either works.
     * @param  int  $maxRetries  Retries per request for retry-safe calls. 0 disables retrying.
     * @param  float  $retryBaseDelay  Seconds for the first backoff step; doubles per attempt.
     * @param  array<string, string>  $defaultHeaders  Sent on every request (e.g. a User-Agent).
     */
    public function __construct(
        public readonly string $clientId,
        public readonly string $clientSecret,
        string $baseUrl,
        public readonly int $maxRetries = 2,
        public readonly float $retryBaseDelay = 0.5,
        public readonly array $defaultHeaders = [],
    ) {
        if ($clientId === '') {
            throw new ConfigurationException('A Puntjes client id is required.');
        }

        if ($clientSecret === '') {
            throw new ConfigurationException('A Puntjes client secret is required.');
        }

        $trimmed = rtrim(trim($baseUrl), '/');

        if ($trimmed === '' || filter_var($trimmed, FILTER_VALIDATE_URL) === false) {
            throw new ConfigurationException(sprintf('"%s" is not a valid Puntjes base URL.', $baseUrl));
        }

        // The documented base URL ends in /api/v1, so that is what an integrator will
        // paste. Strip it back to the host: apiUrl() re-adds the prefix, and tokenUrl()
        // needs a host without it. Accepting both forms is deliberate — this is not
        // error recovery.
        if (str_ends_with($trimmed, self::API_PREFIX)) {
            $trimmed = substr($trimmed, 0, -strlen(self::API_PREFIX));
        }

        if ($maxRetries < 0) {
            throw new ConfigurationException('maxRetries cannot be negative.');
        }

        $this->baseUrl = $trimmed;
    }

    /** Absolute URL for an API path such as `/customers/lookup`. */
    public function apiUrl(string $path): string
    {
        return $this->baseUrl.self::API_PREFIX.'/'.ltrim($path, '/');
    }

    /** Absolute URL of the OAuth token endpoint. */
    public function tokenUrl(): string
    {
        return $this->baseUrl.self::TOKEN_PATH;
    }

    /**
     * Cache key fragment identifying this credential pair, for shared token stores.
     *
     * Hashed so a store that persists keys (Laravel cache, WP options) never
     * writes the client secret anywhere in plaintext.
     */
    public function credentialFingerprint(): string
    {
        return substr(hash('sha256', $this->baseUrl.'|'.$this->clientId.'|'.$this->clientSecret), 0, 32);
    }
}
