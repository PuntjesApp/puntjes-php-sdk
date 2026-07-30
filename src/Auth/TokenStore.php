<?php

declare(strict_types=1);

namespace Puntjes\Auth;

use Puntjes\Config;

/**
 * Where issued access tokens are cached between requests.
 *
 * The default {@see InMemoryTokenStore} lasts one PHP process, which means one
 * token grant per web request — correct, but wasteful. Host applications should
 * supply a shared store instead:
 *
 *   - Laravel   → `Puntjes\Laravel\CacheTokenStore` (ships with `puntjes/laravel`)
 *   - WordPress → a store backed by `set_transient()` / `get_transient()`
 *
 * Implementations must treat the stored value as a secret: it is a bearer token
 * for the whole vendor account. Scope keys per credential — {@see Config::credentialFingerprint()}
 * gives a safe, secret-free fragment for that.
 */
interface TokenStore
{
    /** The cached token for this key, or null when absent. Expiry is checked by the caller. */
    public function get(string $key): ?AccessToken;

    /**
     * Cache a token.
     *
     * @param  int  $ttl  Seconds the value stays valid. Stores with native expiry
     *                    should honour it; the SDK re-checks expiry on read regardless.
     */
    public function put(string $key, AccessToken $token, int $ttl): void;

    /** Drop a token — called when the API rejects it with a 401. */
    public function forget(string $key): void;
}
