<?php

namespace Puntjes\Spike\Kiota\Models;

use Microsoft\Kiota\Abstractions\Enum;

class CreateRewardFromProductData_status extends Enum {
    public const ACTIVE = "active";
    public const INACTIVE = "inactive";
}
