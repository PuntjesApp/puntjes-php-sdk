<?php

declare(strict_types=1);

namespace Puntjes\Request;

/**
 * One order line submitted with a transaction.
 *
 * Line items are optional but worth sending: they are what make the itemized
 * statistics — top products, revenue by category — anything other than empty.
 *
 * The line total is derived server-side as quantity × unitPrice and is not accepted
 * from the client. Prices are in cents.
 */
final class LineItem
{
    public function __construct(
        public readonly string $name,
        public readonly int $quantity,
        /** Unit price in cents. */
        public readonly int $unitPrice,
        /** Your own SKU, ideally matching a catalogue product's external id. */
        public readonly ?string $sku = null,
        public readonly ?string $category = null,
    ) {}

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        $payload = [
            'name' => $this->name,
            'quantity' => $this->quantity,
            'unit_price' => $this->unitPrice,
        ];

        if ($this->sku !== null) {
            $payload['sku'] = $this->sku;
        }

        if ($this->category !== null) {
            $payload['category'] = $this->category;
        }

        return $payload;
    }
}
