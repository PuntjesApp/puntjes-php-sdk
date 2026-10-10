<?php

namespace Puntjes\Spike\Kiota\Models;

use Microsoft\Kiota\Abstractions\Enum;

class DiscountRedemptionData_discount_type extends Enum {
    public const FIXED_AMOUNT = "fixed_amount";
    public const PERCENTAGE = "percentage";
}
