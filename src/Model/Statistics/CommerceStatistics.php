<?php

declare(strict_types=1);

namespace Puntjes\Model\Statistics;

use Puntjes\Support\Cast;

/**
 * Sales figures for the period. All amounts in cents.
 *
 * These headline numbers cover EVERY order in the window, whether or not it carried
 * line-item detail. The {@see $itemized} sub-block covers only the orders that did,
 * and is a different population — its revenue does not sum to {@see $revenueCents}.
 * The difference is reported as `itemized->otherNoItemDetailCents`.
 */
final class CommerceStatistics
{
    /**
     * @param  array<int, VolumeBucket>  $volumeTrend  Zero-filled series at the period's granularity.
     */
    public function __construct(
        public readonly int $orders,
        public readonly int $revenueCents,
        public readonly int $averageOrderValueCents,
        public readonly ItemizedStatistics $itemized,
        public readonly array $volumeTrend,
    ) {}

    /** @param array<array-key, mixed> $data */
    public static function fromArray(array $data): self
    {
        return new self(
            orders: Cast::int($data, 'orders'),
            revenueCents: Cast::int($data, 'revenue_cents'),
            averageOrderValueCents: Cast::int($data, 'average_order_value_cents'),
            itemized: ItemizedStatistics::fromArray(Cast::array($data, 'itemized')),
            volumeTrend: Cast::list($data, 'volume_trend', VolumeBucket::fromArray(...)),
        );
    }
}
