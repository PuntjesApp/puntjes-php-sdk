<?php

declare(strict_types=1);

namespace Puntjes\Model\Statistics;

use Puntjes\Support\Cast;

/**
 * Points movement for the period, from the append-only ledger.
 *
 * The four totals close the wallet-delta identity:
 *
 *     issued − redeemed − expired + netAdjustments
 *
 * so adjustments are surfaced on their own rather than folded into the others.
 * Both rates are null — not zero — when nothing was issued, so an empty period reads
 * as "no data" instead of "0% redemption".
 */
final class LoyaltyStatistics
{
    public function __construct(
        public readonly int $pointsIssued,
        public readonly int $pointsRedeemed,
        /** Points that expired unspent — the breakage. */
        public readonly int $pointsExpired,
        /** Signed sum of manual adjustments; may be negative. */
        public readonly int $netAdjustments,
        /** redeemed ÷ issued, in 0..1. */
        public readonly ?float $redemptionRate,
        /** expired ÷ issued, in 0..1. */
        public readonly ?float $breakageRate,
    ) {}

    /** @param array<array-key, mixed> $data */
    public static function fromArray(array $data): self
    {
        return new self(
            pointsIssued: Cast::int($data, 'points_issued'),
            pointsRedeemed: Cast::int($data, 'points_redeemed'),
            pointsExpired: Cast::int($data, 'points_expired'),
            netAdjustments: Cast::int($data, 'net_adjustments'),
            redemptionRate: Cast::nullableFloat($data, 'redemption_rate'),
            breakageRate: Cast::nullableFloat($data, 'breakage_rate'),
        );
    }

    /** Net change in outstanding points over the period. */
    public function netPointsDelta(): int
    {
        return $this->pointsIssued - $this->pointsRedeemed - $this->pointsExpired + $this->netAdjustments;
    }
}
