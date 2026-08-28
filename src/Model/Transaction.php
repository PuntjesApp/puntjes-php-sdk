<?php

declare(strict_types=1);

namespace Puntjes\Model;

use Puntjes\Support\Cast;

/**
 * A recorded purchase and the points it earned.
 *
 * {@see $pointsEarned} is only meaningful on the response to the call that created
 * the transaction. A replayed `idempotency_key` returns the original transaction
 * with `points_earned: 0` — the points were already awarded by the first call and
 * are not awarded again. Read the wallet if you need the authoritative balance.
 * The same applies when listing historical transactions.
 */
final class Transaction
{
    /**
     * @param  array<int, TransactionItem>  $items
     * @param  array<int, mixed>  $rulesApplied  Earn rules that fired, as returned by the API.
     */
    public function __construct(
        public readonly int $id,
        public readonly int $customerId,
        public readonly string $idempotencyKey,
        public readonly int $totalAmount,
        public readonly ?string $description,
        public readonly ?string $externalReference,
        public readonly string $createdAt,
        public readonly int $pointsEarned,
        public readonly array $rulesApplied,
        public readonly array $items,
        /**
         * The shop this purchase was recorded at, or null for the Unassigned bucket —
         * no branch was named on the request and the credential that sent it defaults
         * to none.
         */
        public readonly ?Branch $branch = null,
    ) {}

    /** @param array<array-key, mixed> $data */
    public static function fromArray(array $data): self
    {
        return new self(
            id: Cast::int($data, 'id'),
            customerId: Cast::int($data, 'customer_id'),
            idempotencyKey: Cast::string($data, 'idempotency_key'),
            totalAmount: Cast::int($data, 'total_amount'),
            description: Cast::nullableString($data, 'description'),
            externalReference: Cast::nullableString($data, 'external_reference'),
            createdAt: Cast::string($data, 'created_at'),
            pointsEarned: Cast::int($data, 'points_earned'),
            rulesApplied: array_values(Cast::array($data, 'rules_applied')),
            items: Cast::list($data, 'items', TransactionItem::fromArray(...)),
            branch: Cast::object($data, 'branch', Branch::fromArray(...)),
        );
    }
}
