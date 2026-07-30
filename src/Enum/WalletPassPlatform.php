<?php

declare(strict_types=1);

namespace Puntjes\Enum;

/**
 * Target wallet app for a pass.
 *
 * The two behave differently on the wire: Apple returns a signed `.pkpass` binary,
 * Google returns JSON holding a save URL to redirect the customer to.
 */
enum WalletPassPlatform: string
{
    case Apple = 'apple';
    case Google = 'google';
}
