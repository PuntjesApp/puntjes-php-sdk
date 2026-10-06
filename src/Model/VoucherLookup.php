<?php

declare(strict_types=1);

namespace Puntjes\Model;

use Puntjes\Enum\VoucherStatus;
use Puntjes\Resource\Vouchers;
use Puntjes\Support\Cast;

/**
 * A campaign bon, read without spending it.
 *
 * Returned by {@see Vouchers::find()}. The bon stays as it was: read {@see $status}
 * first, and spend the bon with {@see Vouchers::verify()} only when it is `Valid`:
 *
 *     $bon = $puntjes->vouchers->find($scannedCode);
 *
 *     if ($bon->status?->isRedeemable()) {
 *         $puntjes->vouchers->verify($scannedCode, idempotencyKey: 'sale-'.$sale->id);
 *     }
 *
 * An expired bon is an answer here, not an error. {@see $status} is null when a newer
 * Puntjes sends a status that this SDK does not know.
 */
final class VoucherLookup
{
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
        /** When the bon was spent, or null while it is not spent. */
        public readonly ?string $consumedAt,
        public readonly int $campaignId,
        public readonly ?VoucherStatus $status,
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
            consumedAt: Cast::nullableString($data, 'consumed_at'),
            campaignId: Cast::int($data, 'campaign_id'),
            status: VoucherStatus::tryFrom(Cast::string($data, 'status')),
        );
    }

    public function isFreeProduct(): bool
    {
        return $this->kind === VoucherVerification::KIND_FREE_PRODUCT;
    }
}
