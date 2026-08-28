<?php

declare(strict_types=1);

namespace Puntjes\Model;

use Puntjes\Resource\Vouchers;
use Puntjes\Support\Cast;

/**
 * A campaign bon that has just been spent.
 *
 * Returned by {@see Vouchers::verify()}, which is a consume rather
 * than a preview — holding this object means the bon is now used and cannot be
 * spent again.
 *
 * Two kinds ride one endpoint. Read {@see $kind} and branch:
 *
 *     $result = $puntjes->vouchers->verify($scannedCode);
 *
 *     if ($result->isFreeProduct()) {
 *         foreach ($result->products as $product) { … }   // hand these over
 *     } else {
 *         $till->discount($result->discount->appliedTo($orderTotal));
 *     }
 */
final class VoucherVerification
{
    public const KIND_DISCOUNT = 'discount';

    public const KIND_FREE_PRODUCT = 'free_product';

    /**
     * @param  array<int, VoucherProduct>|null  $products  Null for a discount bon.
     */
    public function __construct(
        public readonly string $voucherCode,
        /** `discount` or `free_product`. */
        public readonly string $kind,
        /** Null for a free-product bon. */
        public readonly ?VoucherDiscount $discount,
        public readonly ?array $products,
        /** `Y-m-d`, or null when the bon never expires. */
        public readonly ?string $validUntil,
        /** When it was spent — the moment this call succeeded. */
        public readonly string $consumedAt,
        public readonly int $campaignId,
    ) {}

    /** @param array<array-key, mixed> $data */
    public static function fromArray(array $data): self
    {
        return new self(
            voucherCode: Cast::string($data, 'voucher_code'),
            kind: Cast::string($data, 'kind'),
            discount: Cast::object($data, 'discount', VoucherDiscount::fromArray(...)),
            products: Cast::nullableList($data, 'products', VoucherProduct::fromArray(...)),
            validUntil: Cast::nullableString($data, 'valid_until'),
            consumedAt: Cast::string($data, 'consumed_at'),
            campaignId: Cast::int($data, 'campaign_id'),
        );
    }

    public function isFreeProduct(): bool
    {
        return $this->kind === self::KIND_FREE_PRODUCT;
    }
}
