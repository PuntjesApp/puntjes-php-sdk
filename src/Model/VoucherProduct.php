<?php

declare(strict_types=1);

namespace Puntjes\Model;

use Puntjes\Support\Cast;

/**
 * One product a free-product bon entitles the customer to.
 *
 * A snapshot taken when the bon was minted, so it still names what was promised even
 * after the vendor renames or removes the product. {@see $id} is null exactly when
 * the product has since been deleted.
 */
final class VoucherProduct
{
    public function __construct(
        public readonly ?int $id,
        public readonly string $name,
        public readonly int $quantity,
    ) {}

    /** @param array<array-key, mixed> $data */
    public static function fromArray(array $data): self
    {
        return new self(
            id: Cast::nullableInt($data, 'id'),
            name: Cast::string($data, 'name'),
            quantity: Cast::int($data, 'quantity'),
        );
    }
}
