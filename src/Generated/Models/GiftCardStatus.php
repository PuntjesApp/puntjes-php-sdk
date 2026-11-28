<?php

namespace Puntjes\Generated\Models;

use Microsoft\Kiota\Abstractions\Enum;

class GiftCardStatus extends Enum {
    public const OPEN = "open";
    public const USED_UP = "used_up";
    public const ENDED = "ended";
    public const CANCELLED = "cancelled";
}
