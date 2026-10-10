<?php

namespace Puntjes\Spike\Kiota\Models;

use Microsoft\Kiota\Abstractions\Enum;

class TransactionCampaignConfigData_reward_type extends Enum {
    public const MULTIPLIER = "multiplier";
    public const FIXED_POINTS = "fixed_points";
}
