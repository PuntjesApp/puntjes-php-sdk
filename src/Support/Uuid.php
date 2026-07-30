<?php

declare(strict_types=1);

namespace Puntjes\Support;

/**
 * Minimal RFC 4122 version-4 UUID generator.
 *
 * Hand-rolled rather than requiring `ramsey/uuid`: the SDK is meant to drop into a
 * WordPress plugin next to dozens of others, and every avoided dependency is one
 * fewer version conflict to scope away.
 */
final class Uuid
{
    public static function v4(): string
    {
        $bytes = random_bytes(16);

        // Version 4 in the high nibble of byte 6, RFC 4122 variant in byte 8.
        $bytes[6] = chr((ord($bytes[6]) & 0x0F) | 0x40);
        $bytes[8] = chr((ord($bytes[8]) & 0x3F) | 0x80);

        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($bytes), 4));
    }
}
