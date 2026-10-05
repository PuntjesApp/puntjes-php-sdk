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
 * Every rate is null — not zero — when nothing was issued, so an empty period reads
 * as "no data" instead of "0% redemption".
 *
 * Points a customer brought along from another loyalty system (an import) never count
 * as issued, but they do count in `pointsRedeemed` and `pointsExpired` when they are
 * spent or expire. So after an import `redemptionRate` and `breakageRate` can pass 1.0.
 * The `…ExcludingImport` rates leave those points out of both sides: read them first,
 * and fall back to the old rate when an older Puntjes does not send them yet (null).
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
        /** redeemed ÷ issued; can pass 1.0 after an import. */
        public readonly ?float $redemptionRate,
        /** expired ÷ issued; can pass 1.0 after an import. */
        public readonly ?float $breakageRate,
        /** The part of pointsRedeemed that came from an import; 0 from an older Puntjes. */
        public readonly int $pointsRedeemedFromImport = 0,
        /** The part of pointsExpired that came from an import; 0 from an older Puntjes. */
        public readonly int $pointsExpiredFromImport = 0,
        /** (redeemed − redeemed from import) ÷ issued; the rate the Puntjes dashboard shows. */
        public readonly ?float $redemptionRateExcludingImport = null,
        /** (expired − expired from import) ÷ issued; the rate the Puntjes dashboard shows. */
        public readonly ?float $breakageRateExcludingImport = null,
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
            pointsRedeemedFromImport: Cast::int($data, 'points_redeemed_from_import'),
            pointsExpiredFromImport: Cast::int($data, 'points_expired_from_import'),
            redemptionRateExcludingImport: Cast::nullableFloat($data, 'redemption_rate_excluding_import'),
            breakageRateExcludingImport: Cast::nullableFloat($data, 'breakage_rate_excluding_import'),
        );
    }

    /** Net change in outstanding points over the period. */
    public function netPointsDelta(): int
    {
        return $this->pointsIssued - $this->pointsRedeemed - $this->pointsExpired + $this->netAdjustments;
    }
}
