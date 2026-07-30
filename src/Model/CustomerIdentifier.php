<?php

declare(strict_types=1);

namespace Puntjes\Model;

use Puntjes\Enum\IdentifierType;
use Puntjes\Support\Cast;

/** A card, QR code, email or phone number a customer is recognised by. */
final class CustomerIdentifier
{
    public function __construct(
        public readonly int $id,
        public readonly ?IdentifierType $type,
        public readonly string $value,
        public readonly bool $isActive,
        public readonly bool $isPrimary,
        public readonly ?string $createdAt,
        public readonly string $rawType,
    ) {}

    /** @param array<array-key, mixed> $data */
    public static function fromArray(array $data): self
    {
        $rawType = Cast::string($data, 'type');

        return new self(
            id: Cast::int($data, 'id'),
            type: IdentifierType::tryFrom($rawType),
            value: Cast::string($data, 'value'),
            isActive: Cast::bool($data, 'is_active'),
            isPrimary: Cast::bool($data, 'is_primary'),
            createdAt: Cast::nullableString($data, 'created_at'),
            rawType: $rawType,
        );
    }
}
