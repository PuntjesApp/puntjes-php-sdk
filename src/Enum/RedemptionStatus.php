<?php

declare(strict_types=1);

namespace Puntjes\Enum;

/** State of a confirmation code. */
enum RedemptionStatus: string
{
    case Valid = 'valid';
    case Used = 'used';
    case Expired = 'expired';
    /** The shop cancelled the redemption in the admin portal and the customer got the points back. */
    case Cancelled = 'cancelled';

    /** True when the code can still be handed in. Only a valid code can: a cancelled code is final. */
    public function isRedeemable(): bool
    {
        return $this === self::Valid;
    }
}
