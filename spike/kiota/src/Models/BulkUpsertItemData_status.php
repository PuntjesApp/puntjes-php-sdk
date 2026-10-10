<?php

namespace Puntjes\Spike\Kiota\Models;

use Microsoft\Kiota\Abstractions\Enum;

class BulkUpsertItemData_status extends Enum {
    public const ACTIVE = "active";
    public const INACTIVE = "inactive";
    public const ARCHIVED = "archived";
}
