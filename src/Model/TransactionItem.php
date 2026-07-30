<?php

declare(strict_types=1);

namespace Puntjes\Model;

use Puntjes\Support\Cast;

/**
 * One order line of a recorded transaction.
 *
 * `lineTotal` is computed server-side as quantity × unitPrice and is never accepted
 * from the client, so the stored figure is always internally consistent.
 * All amounts are in cents.
 */
final class TransactionItem
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly ?string $sku,
        public readonly int $quantity,
        public readonly int $unitPrice,
        public readonly int $lineTotal,
        public readonly ?string $category,
    ) {}

    /** @param array<array-key, mixed> $data */
    public static function fromArray(array $data): self
    {
        return new self(
            id: Cast::int($data, 'id'),
            name: Cast::string($data, 'name'),
            sku: Cast::nullableString($data, 'sku'),
            quantity: Cast::int($data, 'quantity'),
            unitPrice: Cast::int($data, 'unit_price'),
            lineTotal: Cast::int($data, 'line_total'),
            category: Cast::nullableString($data, 'category'),
        );
    }
}
