<?php

declare(strict_types=1);

namespace Puntjes\Resource;

use Puntjes\Http\Transport;
use Puntjes\Puntjes;

/** Shared plumbing for the endpoint groups hanging off {@see Puntjes}. */
abstract class Resource
{
    public function __construct(protected readonly Transport $transport) {}

    /**
     * URL-encode a path segment.
     *
     * Product SKUs and confirmation codes go into the path and are vendor-supplied,
     * so they can contain slashes and spaces. Encoding them is what keeps
     * `products/A/B` from addressing a route that does not exist.
     */
    protected function segment(string|int $value): string
    {
        return rawurlencode((string) $value);
    }
}
