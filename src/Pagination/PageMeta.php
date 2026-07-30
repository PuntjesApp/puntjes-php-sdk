<?php

declare(strict_types=1);

namespace Puntjes\Pagination;

use Puntjes\Support\Cast;

/** The `meta` block of a paginated list response. */
final class PageMeta
{
    public function __construct(
        public readonly int $currentPage,
        public readonly int $lastPage,
        public readonly int $perPage,
        public readonly int $total,
    ) {}

    /** @param array<array-key, mixed> $data */
    public static function fromArray(array $data): self
    {
        return new self(
            currentPage: Cast::int($data, 'current_page', 1),
            lastPage: Cast::int($data, 'last_page', 1),
            perPage: Cast::int($data, 'per_page', 15),
            total: Cast::int($data, 'total'),
        );
    }

    public function hasMorePages(): bool
    {
        return $this->currentPage < $this->lastPage;
    }
}
