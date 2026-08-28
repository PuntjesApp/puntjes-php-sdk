<?php

declare(strict_types=1);

namespace Puntjes\Model;

use Puntjes\Support\Cast;

/**
 * A campaign the vendor is running, from `GET /campaigns` — active ones only, both
 * currently running and scheduled to start.
 *
 * ## Not every campaign multiplies points
 *
 * {@see $family} says what kind this is. A purchase campaign carries a
 * {@see $multiplier} and a schedule; a customer-moment campaign (a birthday gift, say)
 * carries neither, and sends null for {@see $multiplier}, {@see $recurrenceType} and
 * {@see $recurrenceConfig}. Branch on `family` before reading any of the three.
 *
 * Campaigns are applied server-side when a transaction is recorded. Nothing here has
 * to be passed back in — this endpoint is for showing "double points this Friday" at
 * the till.
 */
final class Campaign
{
    /**
     * @param  array<string, mixed>|null  $config  Verbatim campaign configuration; shape depends on $family.
     * @param  array<string, mixed>|null  $recurrenceConfig  Shape depends on $recurrenceType. Null when the campaign has no schedule.
     * @param  array<int, Branch>|null  $branches  The shops this campaign runs at. Null means all of them — see {@see Branch::scopeFromArray()}.
     */
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        /** Null for a campaign family that has no multiplier, such as a customer moment. */
        public readonly ?int $multiplier,
        public readonly ?string $recurrenceType,
        public readonly ?array $recurrenceConfig,
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
        /** Which kind of campaign this is — read it before trusting {@see $multiplier}. */
        public readonly string $family = '',
        /** For a customer-moment family, which moment triggers it. Null otherwise. */
        public readonly ?string $moment = null,
        public readonly ?array $config = null,
        /** Bumped when the vendor edits the campaign; a voucher records the version it was minted under. */
        public readonly int $version = 0,
        public readonly ?array $branches = null,
    ) {}

    /** @param array<array-key, mixed> $data */
    public static function fromArray(array $data): self
    {
        /** @var array<string, mixed>|null $recurrenceConfig */
        $recurrenceConfig = Cast::nullableArray($data, 'recurrence_config');
        /** @var array<string, mixed>|null $config */
        $config = Cast::nullableArray($data, 'config');

        return new self(
            id: Cast::int($data, 'id'),
            name: Cast::string($data, 'name'),
            multiplier: Cast::nullableInt($data, 'multiplier'),
            recurrenceType: Cast::nullableString($data, 'recurrence_type'),
            recurrenceConfig: $recurrenceConfig,
            scheduleSummary: Cast::string($data, 'schedule_summary'),
            startsAt: Cast::string($data, 'starts_at'),
            endsAt: Cast::nullableString($data, 'ends_at'),
            status: Cast::statusValue($data, 'status'),
            minTransactionAmount: Cast::nullableFloat($data, 'min_transaction_amount'),
            createdAt: Cast::nullableString($data, 'created_at'),
            updatedAt: Cast::nullableString($data, 'updated_at'),
            family: Cast::string($data, 'family'),
            moment: Cast::nullableString($data, 'moment'),
            config: $config,
            version: Cast::int($data, 'version'),
            branches: Branch::scopeFromArray($data),
        );
    }

    /** Whether this campaign runs at every branch, rather than a named few. */
    public function runsEverywhere(): bool
    {
        return $this->branches === null;
    }
}
