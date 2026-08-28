<?php

declare(strict_types=1);

namespace Puntjes\Request;

use Puntjes\Model\Branch;
use Puntjes\Support\Uuid;

/**
 * A reward to exchange for points, for `POST /redemptions`.
 *
 * The idempotency key is scoped per vendor, so reusing one for a different customer
 * or reward is an error the API reports as `IDEMPOTENCY_KEY_CONFLICT` rather than
 * silently returning someone else's confirmation code. Derive keys from something
 * unique to this redemption attempt, never from a fixed string.
 */
final class CreateRedemption
{
    public readonly string $idempotencyKey;

    /**
     * @param  string  $identifier  The customer's scanned loyalty identifier.
     * @param  int  $rewardId  Puntjes reward id, from the reward catalogue.
     * @param  string|null  $branch  The vendor's key for the shop handing the reward over.
     */
    public function __construct(
        public readonly string $identifier,
        public readonly int $rewardId,
        ?string $idempotencyKey = null,
        /**
         * Where the reward is being handed over, by the vendor's own branch key.
         *
         * Omit it and the API falls back to the credential's default branch, then to
         * none. A reward limited to particular branches refuses anywhere else with
         * `BRANCH_REQUIRED` (422); an unknown key is `BRANCH_NOT_FOUND`.
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
            'reward_id' => $this->rewardId,
            'idempotency_key' => $this->idempotencyKey,
        ];

        if ($this->branch !== null) {
            $payload['branch'] = $this->branch;
        }

        return $payload;
    }
}
