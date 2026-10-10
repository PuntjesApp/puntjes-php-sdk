<?php

namespace Puntjes\Spike\Kiota\Statistics;

use Microsoft\Kiota\Abstractions\Enum;

class GetPeriodQueryParameterType extends Enum {
    public const TODAY = "today";
    public const SEVEND = "7d";
    public const THREE_ZEROD = "30d";
    public const NINE_ZEROD = "90d";
}
