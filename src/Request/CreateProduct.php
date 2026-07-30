<?php

declare(strict_types=1);

namespace Puntjes\Request;

use Puntjes\Enum\ProductStatus;

/**
 * A catalogue product to create, for `POST /products`.
 *
 * Creating twice with the same `externalId` fails with `PRODUCT_EXTERNAL_ID_DUPLICATE`.
 * For catalogue sync — where you cannot know whether the SKU already exists — use
 * `products->upsert()` instead, which converges on one row however often it runs.
 */
final class CreateProduct
{
    /**
     * @param  string  $externalId  Your SKU or PLU. The key everything else references.
     * @param  int|null  $priceCents  Price in cents.
     * @param  array<string, mixed>|null  $metadata  Free-form data stored verbatim.
     */
    public function __construct(
        public readonly string $externalId,
        public readonly string $name,
        public readonly ?string $description = null,
        public readonly ?int $priceCents = null,
        public readonly ?string $imageUrl = null,
        public readonly ?string $category = null,
        public readonly ?int $stock = null,
        public readonly ProductStatus $status = ProductStatus::Active,
        public readonly ?array $metadata = null,
    ) {}

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        $payload = [
            'external_id' => $this->externalId,
            'name' => $this->name,
            'status' => $this->status->value,
        ];

        $optional = [
            'description' => $this->description,
            'price_cents' => $this->priceCents,
            'image_url' => $this->imageUrl,
            'category' => $this->category,
            'stock' => $this->stock,
            'metadata' => $this->metadata,
        ];

        foreach ($optional as $key => $value) {
            if ($value !== null) {
                $payload[$key] = $value;
            }
        }

        return $payload;
    }
}
