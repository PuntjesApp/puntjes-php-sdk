<?php

declare(strict_types=1);

namespace Puntjes\Model;

use Puntjes\Enum\CustomerStatus;
use Puntjes\Resource\Customers;
use Puntjes\Support\Cast;

/**
 * A loyalty customer belonging to the authenticated vendor.
 *
 * Two fields are populated only by `customers->lookup()`, which is the till's
 * scan-a-card call and folds the wallet balance in to save a round trip:
 * {@see $walletBalance} and {@see $isDeactivated}. Everywhere else they are null
 * and false — read the balance from `wallet->show()` instead.
 */
final class Customer
{
    /**
     * @param  array<int, CustomerIdentifier>  $identifiers
     */
    public function __construct(
        public readonly int $id,
        public readonly ?string $firstName,
        public readonly ?string $lastName,
        public readonly ?string $email,
        public readonly ?string $phone,
        public readonly ?string $externalId,
        public readonly ?string $dateOfBirth,
        public readonly ?string $locale,
        public readonly ?CustomerStatus $status,
        public readonly array $identifiers,
        public readonly ?string $deactivatedAt,
        public readonly ?string $anonymizedAt,
        public readonly ?string $createdAt,
        public readonly ?string $updatedAt,
        public readonly ?int $walletBalance = null,
        public readonly bool $isDeactivated = false,
        /**
         * The vendor's own "customer since" override, as `Y-m-d`, or null when none
         * is set.
         *
         * Null is not "unknown": it means count tenure from {@see $createdAt}, which
         * this same payload carries.
         */
        public readonly ?string $customerSince = null,
        /**
         * The customer's loyalty-card code — the value to encode in their QR.
         *
         * Persist this the moment {@see Customers::register()} returns,
         * rather than digging it back out of {@see $identifiers}. Null only for customers
         * migrated from before the loyalty-card table existed, and for anonymized ones.
         */
        public readonly ?string $loyaltyCardCode = null,
        public readonly MarketingConsent $marketingConsent = new MarketingConsent,
    ) {}

    /** @param array<array-key, mixed> $data */
    public static function fromArray(array $data): self
    {
        return new self(
            id: Cast::int($data, 'id'),
            firstName: Cast::nullableString($data, 'first_name'),
            lastName: Cast::nullableString($data, 'last_name'),
            email: Cast::nullableString($data, 'email'),
            phone: Cast::nullableString($data, 'phone'),
            externalId: Cast::nullableString($data, 'external_id'),
            dateOfBirth: Cast::nullableString($data, 'date_of_birth'),
            locale: Cast::nullableString($data, 'locale'),
            status: CustomerStatus::tryFrom(Cast::statusValue($data, 'status') ?? ''),
            identifiers: Cast::list($data, 'identifiers', CustomerIdentifier::fromArray(...)),
            deactivatedAt: Cast::nullableString($data, 'deactivated_at'),
            anonymizedAt: Cast::nullableString($data, 'anonymized_at'),
            createdAt: Cast::nullableString($data, 'created_at'),
            updatedAt: Cast::nullableString($data, 'updated_at'),
            walletBalance: Cast::nullableInt($data, 'wallet_balance'),
            isDeactivated: Cast::bool($data, 'is_deactivated'),
            customerSince: Cast::nullableString($data, 'customer_since'),
            loyaltyCardCode: Cast::nullableString($data, 'loyalty_card_code'),
            marketingConsent: MarketingConsent::fromCustomer($data),
        );
    }

    /** Full name, or null when neither name part is set. */
    public function fullName(): ?string
    {
        $name = trim(($this->firstName ?? '').' '.($this->lastName ?? ''));

        return $name === '' ? null : $name;
    }

    /** Whether this customer may be sent marketing email right now. */
    public function hasMarketingConsent(): bool
    {
        return $this->marketingConsent->granted;
    }

    /** The identifier marked primary, if the vendor set one. */
    public function primaryIdentifier(): ?CustomerIdentifier
    {
        foreach ($this->identifiers as $identifier) {
            if ($identifier->isPrimary) {
                return $identifier;
            }
        }

        return null;
    }
}
