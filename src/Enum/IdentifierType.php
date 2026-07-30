<?php

declare(strict_types=1);

namespace Puntjes\Enum;

/** How a customer identifies themselves at the till or checkout. */
enum IdentifierType: string
{
    case Card = 'card';
    case Email = 'email';
    case Phone = 'phone';
    case Qr = 'qr';
    case Nfc = 'nfc';
    case Barcode = 'barcode';
}
