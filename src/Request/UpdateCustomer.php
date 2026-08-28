<?php

declare(strict_types=1);

namespace Puntjes\Request;

use Puntjes\Support\Undefined;

/**
 * Partial update for `PUT|PATCH /customers/by-external-id/{externalId}`.
 *
 * Only supplied fields change. Because several are nullable, omission and null mean
 * different things, and the default is {@see Undefined} rather than null:
 *
 *     new UpdateCustomer(email: 'new@example.com')   // changes only the email
 *     new UpdateCustomer(phone: null)                // clears the phone number
 *
 * The external id is the key and lives in the URL, so it cannot be changed here.
 * Loyalty identifiers are managed separately — cards are not touched by this call.
 */
final class UpdateCustomer
{
    public function __construct(
        public readonly string|null|Undefined $firstName = new Undefined,
        public readonly string|null|Undefined $lastName = new Undefined,
        public readonly string|null|Undefined $email = new Undefined,
        public readonly string|null|Undefined $phone = new Undefined,
        /** `Y-m-d`. */
        public readonly string|null|Undefined $dateOfBirth = new Undefined,
        /** `nl` or `en`. */
        public readonly string|null|Undefined $locale = new Undefined,
        /** `Y-m-d`. Null clears the override, putting tenure back on the registration date. */
        public readonly string|null|Undefined $customerSince = new Undefined,
        /**
         * Record an opt-in (true) or a withdrawal (false). The API timestamps the
         * change and records that it came from the API, so the vendor can show when
         * and where consent moved.
         */
        public readonly bool|null|Undefined $marketingConsent = new Undefined,
    ) {}

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return Undefined::prune([
            'first_name' => $this->firstName,
            'last_name' => $this->lastName,
            'email' => $this->email,
            'phone' => $this->phone,
            'date_of_birth' => $this->dateOfBirth,
            'locale' => $this->locale,
            'customer_since' => $this->customerSince,
            'marketing_consent' => $this->marketingConsent,
        ]);
    }
}
