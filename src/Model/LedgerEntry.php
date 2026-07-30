<?php

declare(strict_types=1);

namespace Puntjes\Model;

use Puntjes\Enum\LedgerEntryType;
use Puntjes\Support\Cast;

/**
 * One append-only movement on a wallet.
 *
 * `amount` is signed relative to the balance — positive for earns, negative for
 * redemptions and expiries. `runningBalance` is the balance immediately after this
 * entry, so a statement never has to be re-summed client-side.
 */
final class LedgerEntry
{
    public function __construct(
        public readonly int $id,
        public readonly int $walletId,
        public readonly ?LedgerEntryType $type,
        public readonly int $amount,
        public readonly int $runningBalance,
        public readonly ?string $reason,
        /** `api_client` for API-initiated adjustments, `user` for admin-portal ones. */
        public readonly ?string $causerType,
        public readonly ?string $causerId,
        public readonly string $createdAt,
        public readonly string $rawType,
    ) {}

    /** @param array<array-key, mixed> $data */
    public static function fromArray(array $data): self
    {
        $rawType = Cast::string($data, 'type');

        return new self(
            id: Cast::int($data, 'id'),
            walletId: Cast::int($data, 'wallet_id'),
            type: LedgerEntryType::tryFrom($rawType),
            amount: Cast::int($data, 'amount'),
            runningBalance: Cast::int($data, 'running_balance'),
            reason: Cast::nullableString($data, 'reason'),
            causerType: Cast::nullableString($data, 'causer_type'),
            causerId: Cast::nullableString($data, 'causer_id'),
            createdAt: Cast::string($data, 'created_at'),
            rawType: $rawType,
        );
    }
}
