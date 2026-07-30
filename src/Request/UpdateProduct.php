<?php

declare(strict_types=1);

namespace Puntjes\Request;

use Puntjes\Enum\ProductStatus;
use Puntjes\Support\Undefined;

/**
 * Partial update for `PATCH /products/{externalId}`.
 *
 * Only supplied fields change; everything else is left as-is. As with
 * {@see UpdateCustomer}, omission and null are distinct — `stock: null` clears the
 * stock figure, omitting it leaves it alone.
 *
 * The external id is immutable: it is the key the catalogue is synced on.
 */
final class UpdateProduct
{
    /**
     * @param  int|null|Undefined  $priceCents  Price in cents.
     * @param  array<string, mixed>|null|Undefined  $metadata
     */
    public function __construct(
        public readonly string|Undefined $name = new Undefined,
        public readonly string|null|Undefined $description = new Undefined,
        public readonly int|null|Undefined $priceCents = new Undefined,
        public readonly string|null|Undefined $imageUrl = new Undefined,
        public readonly string|null|Undefined $category = new Undefined,
        public readonly int|null|Undefined $stock = new Undefined,
        public readonly ProductStatus|Undefined $status = new Undefined,
        public readonly array|null|Undefined $metadata = new Undefined,
    ) {}

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return Undefined::prune([
            'name' => $this->name,
            'description' => $this->description,
            'price_cents' => $this->priceCents,
            'image_url' => $this->imageUrl,
            'category' => $this->category,
            'stock' => $this->stock,
            'status' => $this->status instanceof ProductStatus ? $this->status->value : $this->status,
            'metadata' => $this->metadata,
        ]);
    }
}
