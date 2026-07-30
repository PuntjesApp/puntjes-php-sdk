<?php

declare(strict_types=1);

namespace Puntjes\Enum;

/**
 * What a redeemed reward gives the customer. The redemption response carries a
 * matching `type_specific_data` payload — discount value and unit for a discount,
 * the product reference for a free product.
 */
enum RewardType: string
{
    case Discount = 'discount';
    case FreeProduct = 'free_product';
}
