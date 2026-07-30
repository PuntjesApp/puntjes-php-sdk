<?php

declare(strict_types=1);

namespace Puntjes\Exception;

/**
 * 5xx — the API failed. Retry-safe calls are retried automatically before this
 * surfaces. Quote requestId() when reporting it.
 */
final class ServerException extends ApiException {}
