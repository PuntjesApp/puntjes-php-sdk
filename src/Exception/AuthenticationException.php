<?php

declare(strict_types=1);

namespace Puntjes\Exception;

/**
 * 401: the API did not accept the call. Read the code to know what to fix.
 *
 *   - `UNAUTHENTICATED`: the token is missing, unreadable, expired or revoked. A new
 *     token fixes it. The transport already got one and tried again once, so seeing
 *     this means the new token was refused too: the credentials are wrong or revoked.
 *   - `INVALID_CLIENT`: the token is valid, but the client is wrong: it has no vendor,
 *     or it cannot use client credentials. A new token does not fix it; fix the client
 *     in the Puntjes portal. The same code comes from the token route when the client
 *     id or secret is wrong.
 *
 * A Puntjes from before PuntjesApp/Puntjes#1084 answered `INVALID_CLIENT` for an
 * expired token too, so the transport gets a new token on every first 401, whatever
 * the code.
 */
final class AuthenticationException extends ApiException {}
