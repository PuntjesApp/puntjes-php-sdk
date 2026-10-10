<?php

namespace Puntjes\Spike\Kiota\Customers\Item\Redemptions;

use Microsoft\Kiota\Abstractions\Enum;

class GetStatusQueryParameterType extends Enum {
    public const VALID = "valid";
    public const USED = "used";
    public const EXPIRED = "expired";
    public const CANCELLED = "cancelled";
}
