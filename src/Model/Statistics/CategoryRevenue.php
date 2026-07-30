<?php

declare(strict_types=1);

namespace Puntjes\Model\Statistics;

use Puntjes\Support\Cast;

/** Revenue for one product category over the period. */
final class CategoryRevenue
{
    public function __construct(
        public readonly string $category,
        public readonly int $units,
        /** Revenue in cents. */
        public readonly int $revenue,
    ) {}

    /** @param array<array-key, mixed> $data */
    public static function fromArray(array $data): self
    {
        return new self(
            category: Cast::string($data, 'category'),
            units: Cast::int($data, 'units'),
            revenue: Cast::int($data, 'revenue'),
        );
    }
}
