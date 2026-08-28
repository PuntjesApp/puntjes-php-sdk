<?php

declare(strict_types=1);

namespace Puntjes\Model;

use Puntjes\Support\Cast;

/**
 * Whether a customer may be emailed, and the paper trail behind that answer.
 *
 * {@see $granted} is the decided answer — the API applies the grant-versus-withdrawal
 * rule so no integrator has to re-implement it and get it subtly wrong. The four
 * fields beside it are what makes the consent demonstrable: enough to render
 * "opted in on 3 March via the webshop" without a second request.
 *
 * On the wire these five live at the top level of the customer payload, and they are
 * the only camelCase keys on this API. That spelling is a deliberate part of the
 * contract, not an oversight, so it is read here rather than guessed at.
 */
final class MarketingConsent
{
    public function __construct(
        /** May this person be emailed marketing right now? */
        public readonly bool $granted = false,
        public readonly ?string $grantedAt = null,
        /** Where the opt-in came from — the portal, a webshop, an import. */
        public readonly ?string $grantedSource = null,
        public readonly ?string $withdrawnAt = null,
        public readonly ?string $withdrawnSource = null,
    ) {}

    /**
     * Read the five consent keys off a customer payload.
     *
     * @param  array<array-key, mixed>  $data  The whole customer row, not a sub-object.
     */
    public static function fromCustomer(array $data): self
    {
        return new self(
            granted: Cast::bool($data, 'marketingConsent'),
            grantedAt: Cast::nullableString($data, 'marketingConsentGrantedAt'),
            grantedSource: Cast::nullableString($data, 'marketingConsentGrantedSource'),
            withdrawnAt: Cast::nullableString($data, 'marketingConsentWithdrawnAt'),
            withdrawnSource: Cast::nullableString($data, 'marketingConsentWithdrawnSource'),
        );
    }
}
