<?php

declare(strict_types=1);

namespace Puntjes\Model\Statistics;

use Puntjes\Support\Cast;

/**
 * One bucket of the order-volume trend.
 *
 * The series is zero-filled: every bucket in the window is present, including quiet
 * ones, so a chart never has to interpolate gaps.
 */
final class VolumeBucket
{
    public function __construct(
        /** Bucket label at the period's granularity, e.g. `2026-07-30` or `14:00`. */
        public readonly string $label,
        public readonly int $orders,
        /** Revenue in cents. */
        public readonly int $revenue,
    ) {}

    /** @param array<array-key, mixed> $data */
    public static function fromArray(array $data): self
    {
        return new self(
            label: Cast::string($data, 'label'),
            orders: Cast::int($data, 'orders'),
            revenue: Cast::int($data, 'revenue'),
        );
    }
}
