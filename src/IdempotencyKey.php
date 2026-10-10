<?php

declare(strict_types=1);

namespace Puntjes;

use Puntjes\Support\Uuid;

/**
 * A fresh key for a POST that moves points: the API replays the first answer for a key it has seen.
 */
final class IdempotencyKey
{
    public static function new(): string
    {
        return Uuid::v4();
    }
}
