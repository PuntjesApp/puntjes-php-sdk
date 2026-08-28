<?php

declare(strict_types=1);

namespace Puntjes\Enum;

use Puntjes\Model\Branch;

/**
 * What kind of place a branch is.
 *
 * Carried on the public API because a till has to be able to answer a customer
 * before they queue: "this voucher is webshop-only" is only sayable when the client
 * can tell an `online` branch from a `physical` one.
 *
 * A value this SDK does not know yet resolves to null on {@see Branch::$type} while
 * {@see Branch::$rawType} keeps the wire string, so a new server-side type can never
 * break an older client.
 */
enum BranchType: string
{
    case Physical = 'physical';
    case Online = 'online';
    case Kiosk = 'kiosk';
    case Other = 'other';
}
