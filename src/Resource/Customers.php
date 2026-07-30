<?php

declare(strict_types=1);

namespace Puntjes\Resource;

use Puntjes\Exception\ConfigurationException;
use Puntjes\Exception\ConflictException;
use Puntjes\Exception\NotFoundException;
use Puntjes\Model\Customer;
use Puntjes\Request\CreateCustomer;
use Puntjes\Request\UpdateCustomer;

/** Customer registration and lookup. */
final class Customers extends Resource
{
    /**
     * Find a customer by a scanned loyalty identifier, or by the external id that
     * maps to your own system. Supply exactly one.
     *
     * The response carries `walletBalance`, so scanning a card at the till needs one
     * call rather than two.
     *
     * Deactivated customers are returned with `isDeactivated` set — they exist and
     * can be shown, but cannot transact. Anonymized ones are reported as not found,
     * deliberately: confirming they once existed would defeat the erasure.
     *
     * @throws NotFoundException when no customer matches.
     */
    public function lookup(?string $identifier = null, ?string $externalId = null): Customer
    {
        if (($identifier === null) === ($externalId === null)) {
            throw new ConfigurationException(
                'Look a customer up by exactly one of $identifier or $externalId.',
            );
        }

        $query = $identifier !== null
            ? ['identifier' => $identifier]
            : ['external_id' => $externalId];

        return Customer::fromArray($this->transport->get('/customers/lookup', $query)->dataArray());
    }

    /**
     * Like {@see lookup()}, but returns null instead of throwing when there is no match.
     *
     * The natural shape for a till: "is this card known, and if not, offer to enrol".
     */
    public function findByIdentifier(string $identifier): ?Customer
    {
        try {
            return $this->lookup(identifier: $identifier);
        } catch (NotFoundException) {
            return null;
        }
    }

    /** As {@see findByIdentifier()}, keyed on your own customer id. */
    public function findByExternalId(string $externalId): ?Customer
    {
        try {
            return $this->lookup(externalId: $externalId);
        } catch (NotFoundException) {
            return null;
        }
    }

    /**
     * Register a new customer. Responds 201.
     *
     * A duplicate loyalty identifier or external id is a 409
     * ({@see ConflictException}) — for a POS enrolment flow, check
     * with {@see findByIdentifier()} first.
     */
    public function register(CreateCustomer $customer): Customer
    {
        return Customer::fromArray(
            $this->transport->post('/customers', $customer->toArray())->dataArray(),
        );
    }

    /** Fetch a customer by its Puntjes id. */
    public function find(int $customerId): Customer
    {
        return Customer::fromArray(
            $this->transport->get('/customers/'.$this->segment($customerId))->dataArray(),
        );
    }

    /**
     * Push a change from your own system onto the customer keyed by external id.
     *
     * Only supplied fields change. Loyalty identifiers are not touched — cards are
     * managed on their own routes.
     *
     * @throws NotFoundException (`EXTERNAL_ID_NOT_FOUND`) when the external id is unknown.
     *                           Nothing is created implicitly.
     */
    public function updateByExternalId(string $externalId, UpdateCustomer $changes): Customer
    {
        return Customer::fromArray(
            $this->transport->patch(
                '/customers/by-external-id/'.$this->segment($externalId),
                $changes->toArray(),
            )->dataArray(),
        );
    }
}
