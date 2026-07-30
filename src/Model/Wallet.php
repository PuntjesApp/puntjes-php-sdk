<?php

declare(strict_types=1);

namespace Puntjes\Model;

use Puntjes\Support\Cast;

/** A customer's points balance with this vendor. */
final class Wallet
{
    public function __construct(
        public readonly int $id,
        public readonly int $customerId,
        public readonly int $balance,
        /** Points expiring within the next 30 days — surface this to nudge redemption. */
        public readonly int $expiringSoon,
        public readonly ?string $createdAt,
        public readonly ?string $updatedAt,
    ) {}

    /** @param array<array-key, mixed> $data */
    public static function fromArray(array $data): self
    {
        return new self(
            id: Cast::int($data, 'id'),
            customerId: Cast::int($data, 'customer_id'),
            balance: Cast::int($data, 'balance'),
            expiringSoon: Cast::int($data, 'expiring_soon'),
            createdAt: Cast::nullableString($data, 'created_at'),
            updatedAt: Cast::nullableString($data, 'updated_at'),
        );
    }
}
