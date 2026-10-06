<?php

declare(strict_types=1);

namespace Puntjes\Model;

use Puntjes\Enum\RewardType;
use Puntjes\Support\Cast;

/**
 * A reward as it appears in the customer-facing catalogue (`GET /rewards`).
 *
 * Deliberately a smaller type than {@see Reward}: the catalogue endpoint returns
 * only the fields a till or webshop needs to render a choice, and modelling the
 * missing ones as nullable would suggest the API might send them. It never does.
 */
final class RewardSummary
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly ?string $description,
        public readonly ?RewardType $type,
        public readonly int $pointCost,
        public readonly ?string $imageUrl,
        public readonly int $remainingStock,
        /** Null means unlimited stock. */
        public readonly ?int $totalStock,
        public readonly ?string $availableFrom,
        public readonly ?string $availableUntil,
        public readonly string $rawType,
        /**
         * The shops this reward may be redeemed at. Null means anywhere.
         *
         * Redeeming at a branch outside the scope is refused with `BRANCH_REQUIRED`,
         * so a till can grey the reward out rather than letting the customer pick it.
         *
         * @var array<int, Branch>|null
         */
        public readonly ?array $branches = null,
        /**
         * What the till collects on top of the points, in cents. 0 means points only.
         *
         * Show it next to the points ("500 points + € 2,00") so the customer knows before they pick.
         */
        public readonly int $paymentAmount = 0,
        /**
         * The item number of the product the reward is about. Read {@see $type} first: for a discount, the one
         * product it comes off, and null means the whole purchase; for a free product, the product to hand over,
         * and null means the shop gave no item number. Check it before the claim; settle with the redemption.
         */
        public readonly ?string $productReference = null,
    ) {}

    /** @param array<array-key, mixed> $data */
    public static function fromArray(array $data): self
    {
        $rawType = Cast::string($data, 'type');

        return new self(
            id: Cast::int($data, 'id'),
            name: Cast::string($data, 'name'),
            description: Cast::nullableString($data, 'description'),
            type: RewardType::tryFrom($rawType),
            pointCost: Cast::int($data, 'point_cost'),
            imageUrl: Cast::nullableString($data, 'image_url'),
            remainingStock: Cast::int($data, 'remaining_stock'),
            totalStock: Cast::nullableInt($data, 'total_stock'),
            availableFrom: Cast::nullableString($data, 'available_from'),
            availableUntil: Cast::nullableString($data, 'available_until'),
            rawType: $rawType,
            branches: Branch::scopeFromArray($data),
            paymentAmount: Cast::int($data, 'payment_amount'),
            productReference: Cast::nullableString($data, 'product_reference'),
        );
    }

    /** Whether this is a discount on one product, so the till must find that product on the sale before the claim. */
    public function isDiscountOnOneProduct(): bool
    {
        return $this->type === RewardType::Discount && $this->productReference !== null;
    }

    /** Whether this reward is redeemable at every branch, rather than a named few. */
    public function isRedeemableEverywhere(): bool
    {
        return $this->branches === null;
    }

    /** Whether a customer holding this balance can afford the reward right now. */
    public function isAffordableWith(int $balance): bool
    {
        return $balance >= $this->pointCost;
    }
}
