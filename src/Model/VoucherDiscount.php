<?php

declare(strict_types=1);

namespace Puntjes\Model;

use Puntjes\Support\Cast;

/**
 * The money off a kortingsbon, shaped by its kind.
 *
 * The two shapes are deliberately not merged into one `value` key: a percentage is
 * an integer 1–100 and a fixed amount is an integer number of CENTS, so a single
 * shared field would eventually be read as the wrong one. Exactly one of
 * {@see $percentage} and {@see $amountCents} is set.
 *
 * A discount can count on one product instead of the whole purchase: then
 * {@see $productReference} holds that product's item number.
 */
final class VoucherDiscount
{
    public const KIND_PERCENTAGE = 'percentage';

    public const KIND_FIXED = 'fixed';

    public function __construct(
        /** `percentage` or `fixed`. */
        public readonly string $kind,
        /** 1–100, when {@see $kind} is `percentage`. Null otherwise. */
        public readonly ?int $percentage,
        /** Cents off, when {@see $kind} is `fixed`. Null otherwise. */
        public readonly ?int $amountCents,
        /** The item number of the one product the discount is for. Null when it counts on the whole purchase. */
        public readonly ?string $productReference = null,
    ) {}

    /** @param array<array-key, mixed> $data */
    public static function fromArray(array $data): self
    {
        return new self(
            kind: Cast::string($data, 'kind'),
            percentage: Cast::nullableInt($data, 'percentage'),
            amountCents: Cast::nullableInt($data, 'amount_cents'),
            productReference: Cast::nullableString($data, 'product_reference'),
        );
    }

    public function isPercentage(): bool
    {
        return $this->kind === self::KIND_PERCENTAGE;
    }

    /** True when the discount counts on one product only, the one {@see $productReference} names. */
    public function isOnOneProduct(): bool
    {
        return $this->productReference !== null;
    }

    /**
     * What to take off $totalCents, in cents. For a discount on one product, pass the amount it counts on:
     * that product's price, or its line total if the till applies it to every unit. Puntjes leaves that
     * choice to the till. A fixed amount never takes off more than the amount it is given.
     */
    public function appliedTo(int $totalCents): int
    {
        if ($this->isPercentage()) {
            return (int) floor($totalCents * ($this->percentage ?? 0) / 100);
        }

        return min($totalCents, $this->amountCents ?? 0);
    }
}
