<?php

declare(strict_types=1);

namespace Puntjes\Exception;

/**
 * 401 — the token was rejected, or the OAuth client is not linked to a vendor (INVALID_CLIENT).
 *
 * The transport already retries once with a freshly-minted token before raising
 * this, so seeing it means the credentials themselves are wrong or revoked.
 */
final class AuthenticationException extends ApiException {}
