<?php

declare(strict_types=1);

namespace Puntjes\Model\Statistics;

use Puntjes\Support\Cast;

/**
 * The subset of orders that carried line-item detail.
 *
 * Kept separate from the headline commerce figures on purpose: a POS that submits
 * transactions without `items` still counts towards revenue, but contributes nothing
 * here. Add {@see $otherNoItemDetailCents} to the category rows to reconcile back up
 * to headline revenue.
 */
final class ItemizedStatistics
{
    /**
     * @param  array<int, TopProduct>  $topProducts
     * @param  array<int, CategoryRevenue>  $categories  Genuine categories only; the residual is not one of them.
     */
    public function __construct(
        public readonly int $orders,
        public readonly int $revenueCents,
        public readonly float $averageItemsPerOrder,
        /** Headline revenue minus itemized revenue — the "Other (no item detail)" slice. */
        public readonly int $otherNoItemDetailCents,
        public readonly array $topProducts,
        public readonly array $categories,
    ) {}

    /** @param array<array-key, mixed> $data */
    public static function fromArray(array $data): self
    {
        return new self(
            orders: Cast::int($data, 'orders'),
            revenueCents: Cast::int($data, 'revenue_cents'),
            averageItemsPerOrder: Cast::float($data, 'average_items_per_order'),
            otherNoItemDetailCents: Cast::int($data, 'other_no_item_detail_cents'),
            topProducts: Cast::list($data, 'top_products', TopProduct::fromArray(...)),
            categories: Cast::list($data, 'categories', CategoryRevenue::fromArray(...)),
        );
    }
}
