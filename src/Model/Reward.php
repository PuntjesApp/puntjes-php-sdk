<?php

declare(strict_types=1);

namespace Puntjes\Model;

use Puntjes\Enum\RewardType;
use Puntjes\Support\Cast;

/**
 * The full reward record, as returned by `POST /products/{externalId}/reward`.
 *
 * The catalogue listing returns the narrower {@see RewardSummary} instead.
 */
final class Reward
{
    public function __construct(
        public readonly int $id,
        public readonly ?int $productId,
        public readonly string $name,
        public readonly ?string $description,
        public readonly ?RewardType $type,
        public readonly int $pointCost,
        public readonly ?string $imageUrl,
        public readonly ?int $totalStock,
        public readonly int $remainingStock,
        public readonly ?string $status,
        public readonly ?string $availableFrom,
        public readonly ?string $availableUntil,
        /** Discount rewards only: the amount, in cents or percent per $discountType. */
        public readonly ?int $discountValue,
        public readonly ?string $discountType,
        /** Free-product rewards only: the vendor's own reference for the product. */
        public readonly ?string $productReference,
        /** How long a confirmation code stays valid. Null means it does not expire. */
        public readonly ?int $codeValidForHours,
        public readonly ?string $createdAt,
        public readonly ?string $updatedAt,
        public readonly string $rawType,
        /** What the till collects on top of the points, in cents. 0 means points only. */
        public readonly int $paymentAmount = 0,
        /** True when the reward has no stock limit. {@see $remainingStock} is then 0. */
        public readonly bool $isUnlimited = false,
    ) {}

    /** @param array<array-key, mixed> $data */
    public static function fromArray(array $data): self
    {
        $rawType = Cast::string($data, 'type');
        $totalStock = Cast::nullableInt($data, 'total_stock');

        return new self(
            id: Cast::int($data, 'id'),
            productId: Cast::nullableInt($data, 'product_id'),
            name: Cast::string($data, 'name'),
            description: Cast::nullableString($data, 'description'),
            type: RewardType::tryFrom($rawType),
            pointCost: Cast::int($data, 'point_cost'),
            imageUrl: Cast::nullableString($data, 'image_url'),
            totalStock: $totalStock,
            remainingStock: Cast::int($data, 'remaining_stock'),
            status: Cast::statusValue($data, 'status'),
            availableFrom: Cast::nullableString($data, 'available_from'),
            availableUntil: Cast::nullableString($data, 'available_until'),
            discountValue: Cast::nullableInt($data, 'discount_value'),
            discountType: Cast::nullableString($data, 'discount_type'),
            productReference: Cast::nullableString($data, 'product_reference'),
            codeValidForHours: Cast::nullableInt($data, 'code_valid_for_hours'),
            createdAt: Cast::nullableString($data, 'created_at'),
            updatedAt: Cast::nullableString($data, 'updated_at'),
            rawType: $rawType,
            paymentAmount: Cast::int($data, 'payment_amount'),
            // A Puntjes from before `is_unlimited` sends no such key; `total_stock` is
            // null exactly when a reward has no stock limit, so it gives the same answer.
            isUnlimited: Cast::bool($data, 'is_unlimited', $totalStock === null),
        );
    }
}
