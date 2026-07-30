<?php

declare(strict_types=1);

namespace Puntjes\Model;

use Puntjes\Support\Cast;

/** One row of a {@see BatchUpsertResult}: `created`, `updated`, or `error`. */
final class BatchUpsertItem
{
    /**
     * @param  array<string, array<int, string>>  $errors  Field-keyed messages when status is `error`.
     */
    public function __construct(
        /** Zero-based position in the submitted batch. */
        public readonly int $index,
        public readonly ?string $externalId,
        public readonly string $status,
        public readonly ?Product $product,
        public readonly array $errors,
    ) {}

    /** @param array<array-key, mixed> $data */
    public static function fromArray(array $data): self
    {
        /** @var array<string, array<int, string>> $errors */
        $errors = Cast::array($data, 'errors');

        return new self(
            index: Cast::int($data, 'index'),
            externalId: Cast::nullableString($data, 'external_id'),
            status: Cast::string($data, 'status'),
            product: Cast::object($data, 'product', Product::fromArray(...)),
            errors: $errors,
        );
    }

    public function failed(): bool
    {
        return $this->status === 'error';
    }

    public function wasCreated(): bool
    {
        return $this->status === 'created';
    }

    public function wasUpdated(): bool
    {
        return $this->status === 'updated';
    }
}
