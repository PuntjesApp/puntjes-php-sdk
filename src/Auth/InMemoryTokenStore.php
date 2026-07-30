<?php

declare(strict_types=1);

namespace Puntjes\Auth;

/**
 * Default store: keeps tokens for the lifetime of the PHP process.
 *
 * Correct but not shared — a typical PHP-FPM setup grants one token per web
 * request. Fine for CLI scripts, queue workers and tests; swap in a persistent
 * store for a web application.
 */
final class InMemoryTokenStore implements TokenStore
{
    /** @var array<string, AccessToken> */
    private array $tokens = [];

    public function get(string $key): ?AccessToken
    {
        return $this->tokens[$key] ?? null;
    }

    public function put(string $key, AccessToken $token, int $ttl): void
    {
        $this->tokens[$key] = $token;
    }

    public function forget(string $key): void
    {
        unset($this->tokens[$key]);
    }
}
