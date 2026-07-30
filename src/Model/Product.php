<?php

declare(strict_types=1);

namespace Puntjes\Model;

use Puntjes\Enum\ProductStatus;
use Puntjes\Support\Cast;

/**
 * A catalogue product.
 *
 * Products are keyed on the vendor's own `externalId` (SKU/PLU), not on the Puntjes
 * `id` — that is what makes `PUT /products/{externalId}` a safe sync primitive from
 * a POS that has no knowledge of Puntjes ids.
 */
final class Product
{
    /**
     * @param  array<string, mixed>|null  $metadata  Free-form vendor data, stored verbatim.
     */
    public function __construct(
        public readonly int $id,
        public readonly string $externalId,
        public readonly string $name,
        public readonly ?string $description,
        /** Price in cents. */
        public readonly ?int $priceCents,
        public readonly ?string $imageUrl,
        public readonly ?string $category,
        public readonly ?int $stock,
        public readonly ?ProductStatus $status,
        public readonly ?array $metadata,
        public readonly ?string $createdAt,
        public readonly ?string $updatedAt,
        public readonly string $rawStatus,
    ) {}

    /** @param array<array-key, mixed> $data */
    public static function fromArray(array $data): self
    {
        $rawStatus = Cast::statusValue($data, 'status') ?? '';

        /** @var array<string, mixed>|null $metadata */
        $metadata = Cast::nullableArray($data, 'metadata');

        return new self(
            id: Cast::int($data, 'id'),
            externalId: Cast::string($data, 'external_id'),
            name: Cast::string($data, 'name'),
            description: Cast::nullableString($data, 'description'),
            priceCents: Cast::nullableInt($data, 'price_cents'),
            imageUrl: Cast::nullableString($data, 'image_url'),
            category: Cast::nullableString($data, 'category'),
            stock: Cast::nullableInt($data, 'stock'),
            status: ProductStatus::tryFrom($rawStatus),
            metadata: $metadata,
            createdAt: Cast::nullableString($data, 'created_at'),
            updatedAt: Cast::nullableString($data, 'updated_at'),
            rawStatus: $rawStatus,
        );
    }
}
