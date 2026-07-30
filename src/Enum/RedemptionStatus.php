<?php

declare(strict_types=1);

namespace Puntjes\Enum;

/** State of a confirmation code. */
enum RedemptionStatus: string
{
    case Valid = 'valid';
    case Used = 'used';
    case Expired = 'expired';

    /** True when the code can still be handed in. */
    public function isRedeemable(): bool
    {
        return $this === self::Valid;
    }
}
