<?php

declare(strict_types=1);

namespace Puntjes\Model;

use Puntjes\Model\Statistics\CommerceStatistics;
use Puntjes\Model\Statistics\LoyaltyStatistics;
use Puntjes\Model\Statistics\StatisticsPeriod;
use Puntjes\Support\Cast;

/**
 * The `GET /statistics` envelope: window metadata, sales, and loyalty.
 *
 * The two blocks answer different questions and are deliberately not merged —
 * `commerce` comes from recorded transactions, `loyalty` from the points ledger.
 *
 * Under a `branch:` filter {@see $loyalty} is null and `commerce` covers that shop
 * alone. Check for null before reading it.
 */
final class Statistics
{
    public function __construct(
        public readonly StatisticsPeriod $period,
        public readonly CommerceStatistics $commerce,
        /**
         * Null when the request narrowed to a single branch.
         *
         * Not an omission: points liability is a wallet snapshot and breakage is a ratio
         * whose two halves come from different populations, so neither can be narrowed to
         * one shop. Returning the vendor-wide figures beside branch-filtered commerce
         * numbers would be the misreading this null exists to prevent.
         */
        public readonly ?LoyaltyStatistics $loyalty,
    ) {}

    /** @param array<array-key, mixed> $data */
    public static function fromArray(array $data): self
    {
        return new self(
            period: StatisticsPeriod::fromArray(Cast::array($data, 'period')),
            commerce: CommerceStatistics::fromArray(Cast::array($data, 'commerce')),
            loyalty: Cast::object($data, 'loyalty', LoyaltyStatistics::fromArray(...)),
        );
    }
}
