<?php

declare(strict_types=1);

namespace Puntjes\Enum;

use Puntjes\Resource\Vouchers;

/** State of a campaign bon, as {@see Vouchers::find()} reads it. */
enum VoucherStatus: string
{
    case Valid = 'valid';
    case Used = 'used';
    case Expired = 'expired';

    /** True when the till can still spend the bon. */
    public function isRedeemable(): bool
    {
        return $this === self::Valid;
    }
}
