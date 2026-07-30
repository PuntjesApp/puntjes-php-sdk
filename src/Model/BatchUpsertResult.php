<?php

declare(strict_types=1);

namespace Puntjes\Model;

use Puntjes\Support\Cast;

/**
 * Outcome of `POST /products/batch`.
 *
 * The batch endpoint never fails as a whole: each item is upserted independently and
 * a bad item is reported in its own row. The call answers 200 even when every item
 * failed, so callers MUST inspect {@see $failed} / {@see hasFailures()} rather than
 * treating the absence of an exception as success.
 */
final class BatchUpsertResult
{
    /**
     * @param  array<int, BatchUpsertItem>  $results
     */
    public function __construct(
        public readonly array $results,
        public readonly int $total,
        public readonly int $created,
        public readonly int $updated,
        public readonly int $failed,
    ) {}

    /** @param array<array-key, mixed> $data */
    public static function fromArray(array $data): self
    {
        $summary = Cast::array($data, 'summary');

        return new self(
            results: Cast::list($data, 'results', BatchUpsertItem::fromArray(...)),
            total: Cast::int($summary, 'total'),
            created: Cast::int($summary, 'created'),
            updated: Cast::int($summary, 'updated'),
            failed: Cast::int($summary, 'failed'),
        );
    }

    public function hasFailures(): bool
    {
        return $this->failed > 0;
    }

    /**
     * Only the items that failed, for logging or a retry pass.
     *
     * @return array<int, BatchUpsertItem>
     */
    public function failures(): array
    {
        return array_values(array_filter(
            $this->results,
            static fn (BatchUpsertItem $item): bool => $item->failed(),
        ));
    }
}
