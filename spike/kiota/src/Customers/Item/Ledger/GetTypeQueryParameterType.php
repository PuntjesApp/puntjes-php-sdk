<?php

namespace Puntjes\Spike\Kiota\Customers\Item\Ledger;

use Microsoft\Kiota\Abstractions\Enum;

class GetTypeQueryParameterType extends Enum {
    public const EARN = "earn";
    public const ADJUST = "adjust";
    public const REDEEM = "redeem";
    public const EXPIRE = "expire";
}
