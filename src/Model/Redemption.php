<?php

declare(strict_types=1);

namespace Puntjes\Model;

use Puntjes\Enum\RedemptionStatus;
use Puntjes\Enum\RewardType;
use Puntjes\Support\Cast;

/**
 * A reward exchanged for points, identified by its confirmation code.
 *
 * The endpoints that return a redemption send overlapping — not identical —
 * field sets, so some properties are null depending on how you got here.
 * `forCustomer()` sends the same fields as `find()`:
 *
 * | Field              | create() | find() | verify() |
 * |--------------------|----------|--------|----------|
 * | status             | –        | ✓      | ✓        |
 * | remainingBalance   | ✓        | –      | –        |
 * | customerName       | –        | ✓      | –        |
 * | redeemedAt         | ✓        | ✓      | –        |
 * | verifiedAt         | –        | ✓      | ✓        |
 * | expiresAt          | ✓        | ✓      | –        |
 *
 * `create()` omits status because a freshly created redemption is always valid.
 *
 * `typeSpecificData` is a copy made at the moment of redemption. If the vendor edits the
 * reward afterwards, `find()`, `forCustomer()` and `verify()` still answer the old values.
 */
final class Redemption
{
    /**
     * @param  array<string, mixed>  $typeSpecificData  `discount_value` and `discount_type` for a discount;
     *                                                  `product_reference` and `payment_amount` for a free product.
     */
    public function __construct(
        public readonly int $id,
        public readonly string $confirmationCode,
        public readonly ?RedemptionStatus $status,
        public readonly string $rewardName,
        public readonly ?RewardType $rewardType,
        public readonly int $pointsDeducted,
        public readonly array $typeSpecificData,
        public readonly ?int $remainingBalance = null,
        public readonly ?string $customerName = null,
        public readonly ?string $redeemedAt = null,
        public readonly ?string $verifiedAt = null,
        public readonly ?string $expiresAt = null,
    ) {}

    /** @param array<array-key, mixed> $data */
    public static function fromArray(array $data): self
    {
        $reward = Cast::array($data, 'reward');
        $customer = Cast::array($data, 'customer');

        return new self(
            id: Cast::int($data, 'redemption_id'),
            confirmationCode: Cast::string($data, 'confirmation_code'),
            status: RedemptionStatus::tryFrom(Cast::string($data, 'status')),
            rewardName: Cast::string($reward, 'name'),
            rewardType: RewardType::tryFrom(Cast::string($reward, 'type')),
            pointsDeducted: Cast::int($data, 'points_deducted'),
            typeSpecificData: self::stringKeyed(Cast::array($data, 'type_specific_data')),
            remainingBalance: Cast::nullableInt($data, 'remaining_balance'),
            customerName: Cast::nullableString($customer, 'name'),
            redeemedAt: Cast::nullableString($data, 'redeemed_at'),
            verifiedAt: Cast::nullableString($data, 'verified_at'),
            expiresAt: Cast::nullableString($data, 'expires_at'),
        );
    }

    /** True once the code has been handed in and marked used. */
    public function isVerified(): bool
    {
        return $this->verifiedAt !== null || $this->status === RedemptionStatus::Used;
    }

    /** What the till collects on top of the points, in cents. 0 for a discount, or a reward that costs points only. */
    public function paymentAmount(): int
    {
        $amount = $this->typeSpecificData['payment_amount'] ?? 0;

        return is_int($amount) ? $amount : 0;
    }

    /**
     * @param  array<array-key, mixed>  $data
     * @return array<string, mixed>
     */
    private static function stringKeyed(array $data): array
    {
        $result = [];

        foreach ($data as $key => $value) {
            $result[(string) $key] = $value;
        }

        return $result;
    }
}
