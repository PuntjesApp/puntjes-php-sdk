<?php

declare(strict_types=1);

namespace Puntjes\Auth;

/** Supplies the bearer token the transport puts on every authenticated request. */
interface TokenProvider
{
    /**
     * @param  bool  $forceRefresh  Discard any cached token first. The transport sets
     *                              this after the API rejects a token with a 401.
     */
    public function token(bool $forceRefresh = false): AccessToken;
}
