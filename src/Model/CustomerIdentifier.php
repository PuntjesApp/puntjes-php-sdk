<?php

declare(strict_types=1);

namespace Puntjes\Model;

use Puntjes\Enum\IdentifierType;
use Puntjes\Support\Cast;

/**
 * A loyalty card or email address a customer is recognised by, or a phone number on an
 * account migrated before the API retired them.
 *
 * `type` is null when the API sends a value this SDK version has no case for, which is
 * what a client older than the API answers with. `rawType` always carries the wire value,
 * so it is the field to read when a null type would otherwise look like no identifier.
 */
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
