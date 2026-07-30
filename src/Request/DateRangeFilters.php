<?php

declare(strict_types=1);

namespace Puntjes\Request;

/**
 * Date filters shared by the transaction list and the wallet ledger.
 *
 * Both bounds are inclusive and compared against the record's creation timestamp.
 * Accepts anything the server can parse; `Y-m-d` or a full ISO-8601 timestamp are
 * the sensible choices.
 */
final class DateRangeFilters
{
    /**
     * @param  string|null  $type  Ledger only: restrict to `earn`, `adjust`, `redeem` or `expire`.
     */
    public function __construct(
        public readonly ?string $dateFrom = null,
        public readonly ?string $dateTo = null,
        public readonly ?string $type = null,
    ) {}

    /** @return array<string, mixed> */
    public function toQuery(): array
    {
        return array_filter([
            'date_from' => $this->dateFrom,
            'date_to' => $this->dateTo,
            'type' => $this->type,
        ], static fn (mixed $value): bool => $value !== null);
    }
}
