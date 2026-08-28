<?php

declare(strict_types=1);

namespace Puntjes\Request;

use Puntjes\Model\Branch;
use Puntjes\Support\Uuid;

/**
 * A purchase to record against a customer, for `POST /transactions`.
 *
 * ## The idempotency key
 *
 * Required by the API and generated here when you do not supply one. Because it is
 * fixed at construction, the SDK reuses the same key across its own retries — a
 * replay returns the original transaction instead of awarding points twice.
 *
 * That protection only extends as far as the object lives. If your own code catches
 * a failure and rebuilds the request, pass a key derived from something stable in
 * your system — the order id is the natural choice:
 *
 *     new SubmitTransaction(
 *         identifier: $card,
 *         totalAmount: 4200,
 *         idempotencyKey: 'order-'.$order->id,
 *     );
 */
final class SubmitTransaction
{
    public readonly string $idempotencyKey;

    /**
     * @param  string  $identifier  The value of a customer's loyalty identifier — the scanned card, QR or email.
     * @param  int  $totalAmount  Order total in cents. Must be at least 1.
     * @param  array<int, LineItem>  $items  Up to 200 lines.
     * @param  string|null  $externalReference  Your order/receipt number, for reconciliation.
     * @param  string|null  $branch  The vendor's key for the shop this purchase happened at.
     */
    public function __construct(
        public readonly string $identifier,
        public readonly int $totalAmount,
        ?string $idempotencyKey = null,
        public readonly ?string $description = null,
        public readonly ?string $externalReference = null,
        public readonly array $items = [],
        /**
         * Which shop rang this up, by the vendor's own branch key.
         *
         * Omit it and the API falls back to the branch your API credential defaults to,
         * and then to no branch at all — the Unassigned bucket. So a till that only ever
         * serves one shop is better configured once in the portal than made to send this
         * on every call.
         *
         * An unknown key is refused with `BRANCH_NOT_FOUND` and a closed shop with
         * `BRANCH_INACTIVE`, both 422 — the transaction is not recorded either way.
         * {@see Branch::UNASSIGNED} is a filter word and is not valid here.
         */
        public readonly ?string $branch = null,
    ) {
        $this->idempotencyKey = $idempotencyKey ?? Uuid::v4();
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        $payload = [
            'identifier' => $this->identifier,
            'idempotency_key' => $this->idempotencyKey,
            'total_amount' => $this->totalAmount,
        ];

        if ($this->description !== null) {
            $payload['description'] = $this->description;
        }

        if ($this->externalReference !== null) {
            $payload['external_reference'] = $this->externalReference;
        }

        if ($this->branch !== null) {
            $payload['branch'] = $this->branch;
        }

        if ($this->items !== []) {
            $payload['items'] = array_map(
                static fn (LineItem $item): array => $item->toArray(),
                array_values($this->items),
            );
        }

        return $payload;
    }
}
