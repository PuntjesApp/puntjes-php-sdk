<?php

declare(strict_types=1);

namespace Puntjes\Model;

use Puntjes\Support\Cast;

/**
 * A points multiplier active over a schedule (double points on Fridays, and such).
 *
 * `GET /campaigns` lists active campaigns only, both currently running and
 * scheduled to start.
 */
final class Campaign
{
    /**
     * @param  array<string, mixed>  $recurrenceConfig  Shape depends on $recurrenceType.
     */
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        /** Points multiplier applied to qualifying transactions. */
        public readonly int $multiplier,
        public readonly string $recurrenceType,
        public readonly array $recurrenceConfig,
        /** Human-readable schedule, already localised by the API. */
        public readonly string $scheduleSummary,
        public readonly string $startsAt,
        public readonly ?string $endsAt,
        public readonly ?string $status,
        /**
         * Minimum spend for the campaign to apply, in EUROS — not cents. This one
         * field breaks the API-wide cents convention: the server divides by 100
         * before serialising it.
         */
        public readonly ?float $minTransactionAmount,
        public readonly ?string $createdAt,
        public readonly ?string $updatedAt,
    ) {}

    /** @param array<array-key, mixed> $data */
    public static function fromArray(array $data): self
    {
        /** @var array<string, mixed> $recurrenceConfig */
        $recurrenceConfig = Cast::array($data, 'recurrence_config');

        return new self(
            id: Cast::int($data, 'id'),
            name: Cast::string($data, 'name'),
            multiplier: Cast::int($data, 'multiplier'),
            recurrenceType: Cast::string($data, 'recurrence_type'),
            recurrenceConfig: $recurrenceConfig,
            scheduleSummary: Cast::string($data, 'schedule_summary'),
            startsAt: Cast::string($data, 'starts_at'),
            endsAt: Cast::nullableString($data, 'ends_at'),
            status: Cast::statusValue($data, 'status'),
            minTransactionAmount: Cast::nullableFloat($data, 'min_transaction_amount'),
            createdAt: Cast::nullableString($data, 'created_at'),
            updatedAt: Cast::nullableString($data, 'updated_at'),
        );
    }
}
