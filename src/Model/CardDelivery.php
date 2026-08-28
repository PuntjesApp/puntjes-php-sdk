<?php

declare(strict_types=1);

namespace Puntjes\Model;

use Puntjes\Support\Cast;

/**
 * The receipt for a "send this customer their loyalty card" request.
 *
 * {@see $queued} true means the send was accepted and handed to the mail queue — the
 * API answers 202, not 200, because delivery has not happened yet. A bounce later is
 * not visible here.
 */
final class CardDelivery
{
    public function __construct(
        public readonly int $customerId,
        /** How it was sent. `email` is the only channel today. */
        public readonly string $channel,
        public readonly bool $queued,
    ) {}

    /** @param array<array-key, mixed> $data */
    public static function fromArray(array $data): self
    {
        return new self(
            customerId: Cast::int($data, 'customer_id'),
            channel: Cast::string($data, 'channel'),
            queued: Cast::bool($data, 'queued'),
        );
    }
}
