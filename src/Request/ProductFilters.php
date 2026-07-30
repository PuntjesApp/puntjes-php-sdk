<?php

declare(strict_types=1);

namespace Puntjes\Request;

use Puntjes\Enum\ProductStatus;

/** Query filters for `GET /products`. */
final class ProductFilters
{
    /**
     * @param  string|null  $search  Case-insensitive substring match on the product name.
     * @param  int|null  $perPage  1–100. Defaults to 15 server-side.
     */
    public function __construct(
        public readonly ?ProductStatus $status = null,
        public readonly ?string $category = null,
        public readonly ?string $search = null,
        public readonly ?int $perPage = null,
    ) {}

    /** @return array<string, mixed> */
    public function toQuery(): array
    {
        return array_filter([
            'status' => $this->status?->value,
            'category' => $this->category,
            'search' => $this->search,
            'per_page' => $this->perPage,
        ], static fn (mixed $value): bool => $value !== null);
    }
}
