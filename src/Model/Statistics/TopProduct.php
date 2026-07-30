<?php

declare(strict_types=1);

namespace Puntjes\Model\Statistics;

use Puntjes\Support\Cast;

/** A best-selling product over the period, ranked by revenue then units. */
final class TopProduct
{
    public function __construct(
        public readonly string $name,
        public readonly int $units,
        /** Revenue in cents. */
        public readonly int $revenue,
    ) {}

    /** @param array<array-key, mixed> $data */
    public static function fromArray(array $data): self
    {
        return new self(
            name: Cast::string($data, 'name'),
            units: Cast::int($data, 'units'),
            revenue: Cast::int($data, 'revenue'),
        );
    }
}
