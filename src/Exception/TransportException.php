<?php

declare(strict_types=1);

namespace Puntjes\Exception;

/**
 * The request never produced an HTTP response — DNS failure, connection refused,
 * TLS error, timeout — or the response body was not the JSON envelope the API
 * always returns.
 *
 * Distinct from {@see ApiException}: there is no status code or `request_id` to
 * report, and a retry may well succeed.
 */
final class TransportException extends PuntjesException {}
