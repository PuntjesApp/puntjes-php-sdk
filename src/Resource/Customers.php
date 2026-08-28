<?php

declare(strict_types=1);

namespace Puntjes\Resource;

use Puntjes\Exception\ApiException;
use Puntjes\Exception\ConfigurationException;
use Puntjes\Exception\ConflictException;
use Puntjes\Exception\NotFoundException;
use Puntjes\Model\CardDelivery;
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

    /**
     * Attach your own external id to a customer who does not have one yet, found by
     * an identifier they already carry.
     *
     * The backfill call. {@see register()} takes an external id and
     * {@see updateByExternalId()} needs one, so neither reaches a customer who signed
     * up before your integration existed — this does, keyed on their card code or
     * email.
     *
     * Re-sending the SAME external id succeeds and returns the customer, so retrying
     * is safe. A DIFFERENT one is refused with 409 `CUSTOMER_ALREADY_LINKED` rather
     * than overwriting: whatever system owned the old key would otherwise keep sending
     * updates that silently start 404ing.
     *
     * @param  string  $identifier  A card code, QR value or email the customer already has.
     * @param  string  $externalId  Their id in your system.
     *
     * @throws NotFoundException `CUSTOMER_NOT_FOUND` (404) — no customer carries that identifier.
     * @throws ConflictException `CUSTOMER_ALREADY_LINKED`, or `EXTERNAL_ID_DUPLICATE`
     *                           when another customer already holds that external id.
     */
    public function linkExternalId(string $identifier, string $externalId): Customer
    {
        return Customer::fromArray(
            $this->transport->post('/customers/link-external-id', [
                'identifier' => $identifier,
                'external_id' => $externalId,
            ])->dataArray(),
        );
    }

    /**
     * Email a customer their loyalty card — the API equivalent of the portal's
     * "Email pass to customer" button. Responds 202.
     *
     * NOT retried automatically: the send is queued the moment the API accepts it, so
     * replaying a call whose response was merely lost mails the customer twice. The API
     * throttles repeats per customer and answers `CARD_SEND_THROTTLED`; treat that as
     * "already on its way", not as a failure.
     *
     * @param  string|null  $channel  Omit for the default. `email` is the only channel today.
     *
     * @throws NotFoundException `CUSTOMER_NOT_FOUND` (404).
     * @throws ApiException `CUSTOMER_HAS_NO_EMAIL`, `LOYALTY_CARD_NOT_FOUND` or
     *                      `CARD_SEND_THROTTLED`.
     */
    public function sendCard(int $customerId, ?string $channel = null): CardDelivery
    {
        return CardDelivery::fromArray(
            $this->transport->post(
                '/customers/'.$this->segment($customerId).'/send-card',
                $channel === null ? [] : ['channel' => $channel],
            )->dataArray(),
        );
    }

    /**
     * As {@see sendCard()}, keyed on your own customer id rather than the Puntjes one.
     *
     * The form to prefer: the by-external-id routes exist precisely so an integration
     * never has to store a Puntjes primary key.
     *
     * @throws NotFoundException `EXTERNAL_ID_NOT_FOUND` (404).
     */
    public function sendCardByExternalId(string $externalId, ?string $channel = null): CardDelivery
    {
        return CardDelivery::fromArray(
            $this->transport->post(
                '/customers/by-external-id/'.$this->segment($externalId).'/send-card',
                $channel === null ? [] : ['channel' => $channel],
            )->dataArray(),
        );
    }
}
