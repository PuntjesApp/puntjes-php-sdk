<?php

declare(strict_types=1);

namespace Puntjes\Request;

use Puntjes\Exception\ConfigurationException;

/**
 * Registration payload for `POST /customers`.
 *
 * At least one identifier is required — a customer with no way to be recognised at
 * the till cannot earn anything. Set `externalId` to the customer's id in your own
 * system to make the push-sync endpoints usable later.
 */
final class CreateCustomer
{
    /**
     * @param  array<int, CreateIdentifier>  $identifiers  At least one.
     * @param  string|null  $dateOfBirth  `Y-m-d`.
     * @param  string|null  $locale  `nl` or `en`.
     */
    public function __construct(
        public readonly array $identifiers,
        public readonly ?string $firstName = null,
        public readonly ?string $lastName = null,
        public readonly ?string $email = null,
        public readonly ?string $phone = null,
        public readonly ?string $externalId = null,
        public readonly ?string $dateOfBirth = null,
        public readonly ?string $locale = null,
    ) {
        if ($identifiers === []) {
            throw new ConfigurationException(
                'A customer needs at least one identifier — a card, QR code, email or phone number.',
            );
        }
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        $payload = [
            'first_name' => $this->firstName,
            'last_name' => $this->lastName,
            'email' => $this->email,
            'phone' => $this->phone,
            'external_id' => $this->externalId,
            'date_of_birth' => $this->dateOfBirth,
            'locale' => $this->locale,
        ];

        // Null here means "not supplied" — the create endpoint has no partial-update
        // semantics, so sending nulls would only add noise to the request.
        $payload = array_filter($payload, static fn (mixed $value): bool => $value !== null);

        $payload['identifiers'] = array_map(
            static fn (CreateIdentifier $identifier): array => $identifier->toArray(),
            array_values($this->identifiers),
        );

        return $payload;
    }
}
