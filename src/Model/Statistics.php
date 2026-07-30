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
 */
final class Statistics
{
    public function __construct(
        public readonly StatisticsPeriod $period,
        public readonly CommerceStatistics $commerce,
        public readonly LoyaltyStatistics $loyalty,
    ) {}

    /** @param array<array-key, mixed> $data */
    public static function fromArray(array $data): self
    {
        return new self(
            period: StatisticsPeriod::fromArray(Cast::array($data, 'period')),
            commerce: CommerceStatistics::fromArray(Cast::array($data, 'commerce')),
            loyalty: LoyaltyStatistics::fromArray(Cast::array($data, 'loyalty')),
        );
    }
}
