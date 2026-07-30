<?php

declare(strict_types=1);

namespace Puntjes\Request;

/**
 * Turn a catalogue product into a redeemable reward, for
 * `POST /products/{externalId}/reward`.
 *
 * The resulting reward is of type `free_product` and stays linked to the product.
 * Name and description default to the product's own.
 */
final class CreateRewardFromProduct
{
    /**
     * @param  int  $pointCost  What the reward costs to redeem. At least 1.
     * @param  int|null  $totalStock  How many can be redeemed in total. Null means unlimited.
     * @param  string  $status  `active` or `inactive`.
     * @param  string|null  $availableFrom  `Y-m-d`.
     * @param  string|null  $availableUntil  `Y-m-d`.
     * @param  int|null  $codeValidForHours  Confirmation-code lifetime. Null means it never expires.
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
        ];

        foreach ($optional as $key => $value) {
            if ($value !== null) {
                $payload[$key] = $value;
            }
        }

        return $payload;
    }
}
