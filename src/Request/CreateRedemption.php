<?php

declare(strict_types=1);

namespace Puntjes\Request;

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
     */
    public function __construct(
        public readonly string $identifier,
        public readonly int $rewardId,
        ?string $idempotencyKey = null,
    ) {
        $this->idempotencyKey = $idempotencyKey ?? Uuid::v4();
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return [
            'identifier' => $this->identifier,
            'reward_id' => $this->rewardId,
            'idempotency_key' => $this->idempotencyKey,
        ];
    }
}
