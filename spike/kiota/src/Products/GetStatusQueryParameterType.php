<?php

namespace Puntjes\Spike\Kiota\Products;

use Microsoft\Kiota\Abstractions\Enum;

class GetStatusQueryParameterType extends Enum {
    public const ACTIVE = "active";
    public const INACTIVE = "inactive";
    public const ARCHIVED = "archived";
}
