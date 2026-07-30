<?php

declare(strict_types=1);

namespace Puntjes\Auth;

/**
 * An issued `client_credentials` access token and the moment it stops being usable.
 *
 * Expiry is stored as an absolute unix timestamp rather than the `expires_in`
 * lifetime the server returns, so a token that was cached and read back later is
 * evaluated against the wall clock and not against how long ago it was minted.
 */
final class AccessToken
{
    public function __construct(
        public readonly string $accessToken,
        public readonly int $expiresAt,
        public readonly string $tokenType = 'Bearer',
    ) {}

    /**
     * Build from the `POST /oauth/token` response body.
     *
     * @param  array<string, mixed>  $payload
     * @param  int  $issuedAt  Unix timestamp the response was received.
     */
    public static function fromResponse(array $payload, int $issuedAt): self
    {
        $expiresIn = isset($payload['expires_in']) && is_numeric($payload['expires_in'])
            ? (int) $payload['expires_in']
            : 3600;

        return new self(
            accessToken: (string) ($payload['access_token'] ?? ''),
            expiresAt: $issuedAt + $expiresIn,
            tokenType: (string) ($payload['token_type'] ?? 'Bearer'),
        );
    }

    /**
     * @param  int  $leeway  Treat the token as expired this many seconds early, so a
     *                       request is never sent with a token that dies in flight.
     */
    public function isExpired(int $leeway = 0, ?int $now = null): bool
    {
        return ($now ?? time()) >= ($this->expiresAt - $leeway);
    }

    /** Seconds of remaining life, floored at zero. */
    public function expiresIn(?int $now = null): int
    {
        return max(0, $this->expiresAt - ($now ?? time()));
    }

    public function authorizationHeader(): string
    {
        return $this->tokenType.' '.$this->accessToken;
    }

    /** @return array{access_token: string, expires_at: int, token_type: string} */
    public function toArray(): array
    {
        return [
            'access_token' => $this->accessToken,
            'expires_at' => $this->expiresAt,
            'token_type' => $this->tokenType,
        ];
    }

    /** Rehydrate from {@see toArray()} — for stores that persist plain arrays. */
    public static function fromArray(mixed $data): ?self
    {
        if (! is_array($data) || ! isset($data['access_token'], $data['expires_at'])) {
            return null;
        }

        return new self(
            accessToken: (string) $data['access_token'],
            expiresAt: (int) $data['expires_at'],
            tokenType: (string) ($data['token_type'] ?? 'Bearer'),
        );
    }
}
