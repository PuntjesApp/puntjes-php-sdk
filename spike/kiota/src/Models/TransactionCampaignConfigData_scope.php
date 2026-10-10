<?php

namespace Puntjes\Spike\Kiota\Models;

use Microsoft\Kiota\Abstractions\Enum;

class TransactionCampaignConfigData_scope extends Enum {
    public const WHOLE = "whole";
    public const WHOLE_PURCHASE = "whole_purchase";
    public const PRODUCTS = "products";
}
