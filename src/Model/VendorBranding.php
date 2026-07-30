<?php

declare(strict_types=1);

namespace Puntjes\Model;

use Puntjes\Support\Cast;

/**
 * The authenticated vendor's public identity, from `GET /me`.
 *
 * Also the cheapest way to confirm credentials work — see `Puntjes::me()`.
 */
final class VendorBranding
{
    public function __construct(
        public readonly string $displayName,
        public readonly ?string $logoUrl,
        /** Hex colour, e.g. `#4F46E5`. */
        public readonly ?string $brandColorPrimary,
        public readonly ?string $brandColorAccent,
    ) {}

    /** @param array<array-key, mixed> $data */
    public static function fromArray(array $data): self
    {
        return new self(
            displayName: Cast::string($data, 'display_name'),
            logoUrl: Cast::nullableString($data, 'logo_url'),
            brandColorPrimary: Cast::nullableString($data, 'brand_color_primary'),
            brandColorAccent: Cast::nullableString($data, 'brand_color_accent'),
        );
    }
}
