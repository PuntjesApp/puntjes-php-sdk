<?php

declare(strict_types=1);

namespace Puntjes\Enum;

/**
 * How a customer identifies themselves at the till or checkout.
 *
 * The API collapsed this set to email and loyalty_card in a hard cutover, with no alias
 * window: a retired type submitted to `POST /customers` is refused with a 422. The four
 * scan technologies it removed outright (card, qr, nfc, barcode) are absent here for that
 * reason, and because no stored row carries them any more.
 */
enum IdentifierType: string
{
    case Email = 'email';

    case LoyaltyCard = 'loyalty_card';

    /**
     * @deprecated Reads only. The API keeps this case so migrated deactivated rows still
     *             hydrate, and excludes it from what registration accepts. Removing it
     *             here would make those rows decode with a null type.
     */
    case Phone = 'phone';
}
