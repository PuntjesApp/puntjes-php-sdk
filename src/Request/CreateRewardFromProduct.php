<?php

declare(strict_types=1);

namespace Puntjes\Request;

/**
 * Turn a catalogue product into a redeemable reward, for
 * `POST /products/{externalId}/reward`.
 *
 * The resulting reward is of type `free_product` and stays linked to the product.
 * Name and description default to the product's own.
 *
 * ## The idempotency key
 *
 * Optional, and the SDK does not make one for you. With a key, the SDK retries a
 * failed call automatically, and a call with the same key, the same product, the same
 * `pointCost` and the same `paymentAmount` answers the first reward as it is now (the
 * shop may have changed its name, stock or dates since). The same key with another
 * product or another amount, or a key whose reward was deleted, answers
 * `IDEMPOTENCY_KEY_CONFLICT` (422). Without a key, every call creates a new reward and
 * is never retried. A Puntjes from before PuntjesApp/Puntjes#1084 ignores the key, so
 * send one only to a Puntjes that has it.
 */
final class CreateRewardFromProduct
{
    /**
     * @param  int  $pointCost  What the reward costs to redeem. At least 1.
     * @param  int|null  $totalStock  How many can be redeemed in total. Null means unlimited.
     * @param  string  $status  `active` or `inactive`.
     * @param  string|null  $availableFrom  `Y-m-d`.
     * @param  string|null  $availableUntil  `Y-m-d`. Not before `$availableFrom`, or the API answers 422.
     * @param  int|null  $codeValidForHours  Confirmation-code lifetime, 1 to 87600 (ten years). Null means it never
     *                                       expires. A larger value answers 422 `VALIDATION_ERROR`.
     * @param  int|null  $paymentAmount  What the till collects on top of the points, in cents. Null or 0 means points only.
     * @param  string|null  $idempotencyKey  Your own key for this reward, at most 255 characters, unique per vendor.
     * @param  int|null  $maxRedemptionsPerCustomer  How many times one customer can redeem it, at least 1. Null means no limit.
     */
    public function __construct(
        public readonly int $pointCost,
        public readonly ?string $name = null,
        public readonly ?string $description = null,
        public readonly ?int $totalStock = null,
        public readonly string $status = 'active',
        public readonly ?string $availableFrom = null,
        public readonly ?string $availableUntil = null,
        public readonly ?int $codeValidForHours = null,
        public readonly ?int $paymentAmount = null,
        public readonly ?string $idempotencyKey = null,
        public readonly ?int $maxRedemptionsPerCustomer = null,
    ) {}

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        $payload = [
            'point_cost' => $this->pointCost,
            'status' => $this->status,
        ];

        $optional = [
            'name' => $this->name,
            'description' => $this->description,
            'total_stock' => $this->totalStock,
            'available_from' => $this->availableFrom,
            'available_until' => $this->availableUntil,
            'code_valid_for_hours' => $this->codeValidForHours,
            'payment_amount' => $this->paymentAmount,
            'idempotency_key' => $this->idempotencyKey,
            'max_redemptions_per_customer' => $this->maxRedemptionsPerCustomer,
        ];

        foreach ($optional as $key => $value) {
            if ($value !== null) {
                $payload[$key] = $value;
            }
        }

        return $payload;
    }
}
