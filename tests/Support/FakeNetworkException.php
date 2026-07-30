<?php

declare(strict_types=1);

namespace Puntjes\Tests\Support;

use Psr\Http\Client\ClientExceptionInterface;
use RuntimeException;

/** Stands in for a connection-level failure from a real PSR-18 client. */
final class FakeNetworkException extends RuntimeException implements ClientExceptionInterface {}
