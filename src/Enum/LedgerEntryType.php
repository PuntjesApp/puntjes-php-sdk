<?php

declare(strict_types=1);

namespace Puntjes\Enum;

/**
 * Why a wallet's balance moved. The ledger is append-only — entries are never
 * updated or deleted, so the running balance is always reconstructable.
 */
enum LedgerEntryType: string
{
    case Earn = 'earn';
    case Adjust = 'adjust';
    case Redeem = 'redeem';
    case Expire = 'expire';

    /** True when this entry type increases the balance. */
    public function isCredit(): bool
    {
        return $this === self::Earn;
    }
}
