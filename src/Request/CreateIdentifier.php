<?php

declare(strict_types=1);

namespace Puntjes\Request;

use Puntjes\Enum\IdentifierType;

/** An identifier to attach to a new customer. */
final class CreateIdentifier
{
    public function __construct(
        public readonly IdentifierType $type,
        public readonly string $value,
        /** Exactly one identifier should be primary; it is the one shown in the portal. */
        public readonly bool $isPrimary = false,
    ) {}

    /**
     * The value must be a card the vendor has printed and not yet assigned. Matching is
     * case-insensitive; anything else is refused with `LOYALTY_CARD_NOT_FOUND` (422), or
     * `IDENTIFIER_DUPLICATE` (409) when the card already belongs to someone.
     */
    public static function loyaltyCard(string $value, bool $isPrimary = true): self
    {
        return new self(IdentifierType::LoyaltyCard, $value, $isPrimary);
    }

    public static function email(string $value, bool $isPrimary = false): self
    {
        return new self(IdentifierType::Email, $value, $isPrimary);
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return [
            'type' => $this->type->value,
            'value' => $this->value,
            'is_primary' => $this->isPrimary,
        ];
    }
}
