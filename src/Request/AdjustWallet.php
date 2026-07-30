<?php

declare(strict_types=1);

namespace Puntjes\Request;

use Puntjes\Exception\ConfigurationException;
use Puntjes\Support\Uuid;

/**
 * A manual points correction, for `POST /customers/{id}/wallet/adjust`.
 *
 * Positive amounts credit, negative amounts debit; zero is rejected. A debit larger
 * than the balance fails with `INSUFFICIENT_BALANCE`.
 *
 * The reason is stored on the ledger entry and shown in the admin portal — it is the
 * audit trail for a movement no rule produced, so write it for a human.
 */
final class AdjustWallet
{
    public readonly string $idempotencyKey;

    /**
     * @param  int  $amount  Points to add (positive) or remove (negative). Never zero.
     * @param  string  $reason  Why the adjustment was made. Max 500 characters.
     */
    public function __construct(
        public readonly int $amount,
        public readonly string $reason,
        ?string $idempotencyKey = null,
    ) {
        if ($amount === 0) {
            throw new ConfigurationException('A wallet adjustment of zero points has no effect and is rejected by the API.');
        }

        $this->idempotencyKey = $idempotencyKey ?? Uuid::v4();
    }

    public static function credit(int $points, string $reason, ?string $idempotencyKey = null): self
    {
        return new self(abs($points), $reason, $idempotencyKey);
    }

    public static function debit(int $points, string $reason, ?string $idempotencyKey = null): self
    {
        return new self(-abs($points), $reason, $idempotencyKey);
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return [
            'amount' => $this->amount,
            'reason' => $this->reason,
            'idempotency_key' => $this->idempotencyKey,
        ];
    }
}
