<?php

declare(strict_types=1);

namespace Puntjes\Request;

use Puntjes\Enum\ProductStatus;

/**
 * The full product representation for `PUT /products/{externalId}`.
 *
 * PUT replaces the whole resource, so anything you omit is CLEARED, not left alone —
 * that is what makes repeated pushes of a catalogue row converge. Send every field
 * you care about on every push. To change one field, use `products->update()`
 * (PATCH) instead.
 *
 * The external id is the URL key and so is not part of the body.
 */
final class UpsertProduct
{
    /**
     * @param  int|null  $priceCents  Price in cents.
     * @param  array<string, mixed>|null  $metadata
     */
    public function __construct(
        public readonly string $name,
        public readonly ?string $description = null,
        public readonly ?int $priceCents = null,
        public readonly ?string $imageUrl = null,
        public readonly ?string $category = null,
        public readonly ?int $stock = null,
        public readonly ProductStatus $status = ProductStatus::Active,
        public readonly ?array $metadata = null,
    ) {}

    /**
     * Nulls ARE sent here, unlike on create: under replace semantics an omitted
     * nullable field and an explicit null mean the same thing, and being explicit
     * makes the request self-describing.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'description' => $this->description,
            'price_cents' => $this->priceCents,
            'image_url' => $this->imageUrl,
            'category' => $this->category,
            'stock' => $this->stock,
            'status' => $this->status->value,
            'metadata' => $this->metadata,
        ];
    }

    /**
     * The batch endpoint takes the external id inside each item rather than in a URL.
     *
     * @return array<string, mixed>
     */
    public function toBatchArray(string $externalId): array
    {
        return ['external_id' => $externalId] + $this->toArray();
    }
}
