<?php

declare(strict_types=1);

namespace Puntjes\Request;

/**
 * Registration payload for `POST /customers`.
 *
 * Send no identifiers and Puntjes issues the loyalty card itself, which is the common
 * case. Pass one only when the customer already carries something you want to keep, such
 * as a card code printed before they signed up. Set `externalId` to the customer's id in
 * your own system to make the push-sync endpoints usable later.
 */
final class CreateCustomer
{
    /**
     * @param  array<int, CreateIdentifier>  $identifiers  Empty lets Puntjes issue the card.
     * @param  string|null  $dateOfBirth  `Y-m-d`.
     * @param  string|null  $locale  `nl` or `en`.
     * @param  string|null  $customerSince  `Y-m-d`. Overrides the registration date as the tenure start.
     * @param  bool|null  $marketingConsent  Whether they opted in to marketing email. Omit when you did not ask.
     */
    public function __construct(
        public readonly array $identifiers = [],
        public readonly ?string $firstName = null,
        public readonly ?string $lastName = null,
        public readonly ?string $email = null,
        public readonly ?string $phone = null,
        public readonly ?string $externalId = null,
        public readonly ?string $dateOfBirth = null,
        public readonly ?string $locale = null,
        public readonly ?string $customerSince = null,
        /**
         * Only send true when the customer actually opted in on your side — this is
         * the record the vendor relies on to prove consent. Omitting it registers no
         * opt-in, which is the safe default.
         */
        public readonly ?bool $marketingConsent = null,
    ) {}

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
            'customer_since' => $this->customerSince,
            'marketing_consent' => $this->marketingConsent,
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
