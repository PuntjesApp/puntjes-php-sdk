<?php

declare(strict_types=1);

namespace Puntjes\Http\Json;

use Puntjes\Generated\Models\RedemptionCreatedData_type_specific_data;
use Puntjes\Generated\Models\RedemptionLookupData_type_specific_data;
use Puntjes\Generated\Models\RedemptionVerificationData_type_specific_data;

/**
 * The "type" a redemption's type_specific_data stands for when the answer leaves that key out: the spec requires it,
 * but the API's own documented example omits it. A discount field means a discount, else a free-product field means a
 * free product. A "type" that is present, known or not, is never overruled.
 */
final class DiscriminatorFallback
{
    private const WRAPPERS = [
        RedemptionCreatedData_type_specific_data::class,
        RedemptionLookupData_type_specific_data::class,
        RedemptionVerificationData_type_specific_data::class,
    ];

    /**
     * @param  array<array-key, mixed>  $content
     */
    public static function typeFor(string $class, array $content): ?string
    {
        if (! in_array($class, self::WRAPPERS, true) || ($content['type'] ?? null) !== null) {
            return null;
        }

        return match (true) {
            array_key_exists('discount_type', $content), array_key_exists('discount_value', $content) => 'discount',
            array_key_exists('payment_amount', $content), array_key_exists('product_reference', $content) => 'free_product',
            default => null,
        };
    }
}
