<?php

declare(strict_types=1);

namespace Puntjes\Model;

use Puntjes\Enum\BranchType;
use Puntjes\Support\Cast;

/**
 * One of the vendor's shops, as it appears on the public API.
 *
 * Three fields, and the omission is the point: there is no Puntjes id here. A branch
 * is addressed by {@see $externalId} — the key the vendor chose in the portal — so
 * nothing on this surface invites you to store a primary key you would then have to
 * keep in step.
 *
 * That same key is what you send back: `SubmitTransaction(branch: 'centrum')`,
 * `CreateRedemption(branch: 'centrum')`, `$puntjes->statistics->get(branch: 'centrum')`.
 */
final class Branch
{
    /**
     * The reserved key naming the Unassigned bucket — purchases that no branch was
     * recorded for.
     *
     * Valid only where a branch is a *filter* — `$puntjes->statistics->get(branch: …)`
     * and `$puntjes->campaigns->list(branch: …)` — and there it is a third state distinct
     * from "no filter at all": omitting the filter covers the whole vendor, `none` covers
     * only what was never attributed.
     *
     * It is never a valid key on a write. A transaction, a redemption or a voucher names
     * the shop it happened at, and no branch can be keyed `none`, so sending it there is
     * refused with `BRANCH_NOT_FOUND` rather than silently recording revenue against
     * nothing.
     */
    public const UNASSIGNED = 'none';

    public function __construct(
        /** The vendor's own key for this shop — what you pass back to address it. */
        public readonly string $externalId,
        public readonly string $name,
        public readonly ?BranchType $type,
        /** The wire value of {@see $type}, preserved for types newer than this SDK. */
        public readonly string $rawType,
    ) {}

    /** @param array<array-key, mixed> $data */
    public static function fromArray(array $data): self
    {
        $rawType = Cast::string($data, 'type');

        return new self(
            externalId: Cast::string($data, 'external_id'),
            name: Cast::string($data, 'name'),
            type: BranchType::tryFrom($rawType),
            rawType: $rawType,
        );
    }

    /**
     * Read a `branches` scope: a list of branches, or null when there is no scope.
     *
     * The distinction matters and is deliberately preserved rather than normalised:
     * `null` means *every* branch, an empty list means *no* branch matches any more —
     * every shop the scope named has since been deleted. Collapsing the two would
     * advertise a campaign that can never fire as running everywhere.
     *
     * @param  array<array-key, mixed>  $data
     * @return array<int, self>|null
     */
    public static function scopeFromArray(array $data, string $key = 'branches'): ?array
    {
        return Cast::nullableList($data, $key, self::fromArray(...));
    }
}
