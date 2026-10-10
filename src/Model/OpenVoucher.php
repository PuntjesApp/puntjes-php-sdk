<?php

declare(strict_types=1);

namespace Puntjes\Model;

use Puntjes\Resource\Vouchers;
use Puntjes\Support\Cast;

/**
 * A campaign bon that a customer can still spend.
 *
 * Returned by {@see Vouchers::forCustomer()}, for a customer who comes to the till
 * without the code. Show the list, let the customer pick one, and spend it with
 * {@see Vouchers::verify()}:
 *
 *     foreach ($puntjes->vouchers->forCustomer($customer->id) as $bon) {
 *         if ($bon->isSpendableAt('centrum')) { … }   // offer it here
 *     }
 *
 * There is no status: a bon that was spent or ran out is not in the list.
 */
final class OpenVoucher
{
    /**
     * @param  array<int, VoucherProduct>|null  $products  Null for a discount bon.
     * @param  array<int, Branch>|null  $branches  The shops that take the bon. Null means all of them.
     */
    public function __construct(
        public readonly string $voucherCode,
        public readonly int $campaignId,
        /** `discount` or `free_product`. */
        public readonly string $kind,
        /** Null for a free-product bon. */
        public readonly ?VoucherDiscount $discount,
        public readonly ?array $products,
        /** `Y-m-d`, the last day the bon can be spent, or null when it never runs out. */
        public readonly ?string $validUntil,
        public readonly ?array $branches,
    ) {}

    /** @param array<array-key, mixed> $data */
    public static function fromArray(array $data): self
    {
        return new self(
            voucherCode: Cast::string($data, 'voucher_code'),
            campaignId: Cast::int($data, 'campaign_id'),
            kind: Cast::string($data, 'kind'),
            discount: Cast::object($data, 'discount', VoucherDiscount::fromArray(...)),
            products: Cast::nullableList($data, 'products', VoucherProduct::fromArray(...)),
            validUntil: Cast::nullableString($data, 'valid_until'),
            branches: Branch::scopeFromArray($data),
        );
    }

    public function isFreeProduct(): bool
    {
        return $this->kind === VoucherVerification::KIND_FREE_PRODUCT;
    }

    /** Whether every shop of the vendor takes this bon. */
    public function isSpendableEverywhere(): bool
    {
        return $this->branches === null;
    }

    /**
     * Whether the shop with this key takes the bon. Elsewhere, verify answers `BRANCH_REQUIRED`.
     *
     * @param  string  $branch  The vendor's key for the shop, {@see Branch::$externalId}.
     */
    public function isSpendableAt(string $branch): bool
    {
        if ($this->branches === null) {
            return true;
        }

        foreach ($this->branches as $scoped) {
            if ($scoped->externalId === $branch) {
                return true;
            }
        }

        return false;
    }
}
