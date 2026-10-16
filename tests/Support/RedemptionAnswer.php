<?php

declare(strict_types=1);

namespace Puntjes\Tests\Support;

/**
 * The data of a redemption answer, with the type_specific_data a test needs.
 */
final class RedemptionAnswer
{
    /**
     * @param  array<string, mixed>  $typeSpecificData
     * @return array<string, mixed>
     */
    public static function lookup(array $typeSpecificData): array
    {
        return [
            'redemption_id' => 9,
            'confirmation_code' => 'ABC123',
            'status' => 'pending',
            'points_deducted' => 10,
            'reward' => ['id' => 2, 'name' => 'NEW', 'type' => 'discount'],
            'customer' => ['id' => 5],
            'redeemed_at' => '2026-10-16T10:00:00+00:00',
            'expires_at' => '2026-10-17T10:00:00+00:00',
            'verified_at' => null,
            'type_specific_data' => $typeSpecificData,
        ];
    }
}
