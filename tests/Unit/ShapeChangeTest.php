<?php

declare(strict_types=1);

namespace Puntjes\Tests\Unit;

use Puntjes\Enum\IdentifierType;
use Puntjes\Enum\Period;
use Puntjes\Request\CreateRedemption;
use Puntjes\Tests\Support\TestCase;

/**
 * Response shapes the API sends that an earlier SDK typed as impossible.
 *
 * Both of these decoded fine until the day the vendor did something ordinary — created
 * a birthday campaign, or asked for one shop's numbers — and then threw a `TypeError`
 * out of a model constructor. A fixture that only ever carries the happy shape is
 * exactly the blind spot this file exists to hold open.
 */
final class ShapeChangeTest extends TestCase
{
    /**
     * The type the API mints on every customer. An SDK enum without a case for it answers
     * null from `tryFrom`, so the identifier reads as typeless while `rawType` holds the
     * truth, and code filtering by type finds nothing and raises nothing.
     */
    public function test_a_loyalty_card_identifier_decodes_to_its_type(): void
    {
        $this->fake->queueData($this->customerWithIdentifierType('loyalty_card') + ['wallet_balance' => 0]);

        $identifier = $this->puntjes()->customers->lookup(identifier: '7KQ4M2XP')->primaryIdentifier();

        self::assertSame(IdentifierType::LoyaltyCard, $identifier?->type);
        self::assertSame('loyalty_card', $identifier?->rawType);
    }

    /**
     * `phone` is retired for writes and kept for reads: the API's own enum retains it so
     * migrated deactivated rows still hydrate. Dropping it from this SDK would reproduce
     * the null-type defect above on exactly those rows.
     */
    public function test_a_migrated_phone_identifier_still_decodes(): void
    {
        $this->fake->queueData($this->customerWithIdentifierType('phone') + ['wallet_balance' => 0]);

        $identifier = $this->puntjes()->customers->lookup(identifier: '+3230000000')->primaryIdentifier();

        self::assertSame(IdentifierType::Phone, $identifier?->type);
    }

    /** The scan technologies the API removed outright. Nothing stores them, so nothing reads them. */
    public function test_the_retired_scan_types_are_gone(): void
    {
        foreach (['card', 'qr', 'nfc', 'barcode'] as $retired) {
            self::assertNull(IdentifierType::tryFrom($retired), "{$retired} should no longer be a case");
        }
    }

    /** @return array<string, mixed> */
    private function customerWithIdentifierType(string $type): array
    {
        return [
            'id' => 42, 'first_name' => 'Jan', 'last_name' => 'Everaert',
            'email' => 'jan@example.com', 'phone' => null, 'external_id' => 'PNU-1',
            'date_of_birth' => null, 'locale' => 'nl',
            'status' => ['value' => 'active', 'label' => 'Active'],
            'identifiers' => [[
                'id' => 7, 'type' => $type, 'value' => '7KQ4M2XP',
                'is_active' => true, 'is_primary' => true,
                'created_at' => '2026-01-01T00:00:00+00:00',
            ]],
            'deactivated_at' => null, 'anonymized_at' => null,
            'created_at' => '2026-01-01T00:00:00+00:00',
            'updated_at' => '2026-01-01T00:00:00+00:00',
        ];
    }

    public function test_a_campaign_that_multiplies_nothing_still_decodes(): void
    {
        // A customer-moment campaign — a birthday gift — has no multiplier and no
        // schedule, and the API sends null for all three.
        $this->fake->queuePage([[
            'id' => 9, 'name' => 'Verjaardagsbon', 'family' => 'customer_moment', 'moment' => 'birthday',
            'config' => null, 'version' => 1,
            'multiplier' => null, 'recurrence_type' => null, 'recurrence_config' => null,
            'schedule_summary' => 'Op de verjaardag', 'starts_at' => '2026-08-01', 'ends_at' => null,
            'status' => ['value' => 'active', 'label' => 'Actief'],
            'min_transaction_amount' => null,
            'created_at' => null, 'updated_at' => null,
        ]]);

        $campaign = $this->puntjes()->campaigns->list()->firstPage()->first();

        self::assertNull($campaign?->multiplier);
        self::assertNull($campaign?->recurrenceType);
        self::assertNull($campaign?->recurrenceConfig);
        self::assertSame('customer_moment', $campaign?->family);
        self::assertSame('birthday', $campaign?->moment);
    }

    public function test_a_points_campaign_still_reports_its_multiplier(): void
    {
        $this->fake->queuePage([[
            'id' => 1, 'name' => 'Dubbele punten', 'family' => 'purchase', 'moment' => null,
            'config' => null, 'version' => 1,
            'multiplier' => 2, 'recurrence_type' => 'weekly',
            'recurrence_config' => ['days' => ['friday']],
            'schedule_summary' => 'Elke vrijdag', 'starts_at' => '2026-08-01', 'ends_at' => null,
            'status' => ['value' => 'active', 'label' => 'Actief'],
            'min_transaction_amount' => 25.0,
            'created_at' => null, 'updated_at' => null,
        ]]);

        $campaign = $this->puntjes()->campaigns->list()->firstPage()->first();

        self::assertSame(2, $campaign?->multiplier);
        self::assertSame(['days' => ['friday']], $campaign?->recurrenceConfig);
    }

    /**
     * The API sends `whole` from the campaign builder and `whole_purchase` from campaigns older than
     * campaign families, and both mean the whole purchase. An older schedule can store Sunday as 7.
     * The SDK hands all of it over as it came, so a stricter model cannot start refusing a live row.
     */
    public function test_a_campaign_hands_over_either_scope_word_and_a_sunday_seven_as_sent(): void
    {
        $this->fake->queuePage([
            $this->purchaseCampaign(1, ['scope' => 'whole'], ['days' => [6, 7]]),
            $this->purchaseCampaign(2, ['scope' => 'whole_purchase'], ['days' => [6, 0]]),
        ]);

        [$builderMade, $older] = $this->puntjes()->campaigns->list()->firstPage()->items;

        self::assertSame(['scope' => 'whole'], $builderMade->config);
        self::assertSame(['days' => [6, 7]], $builderMade->recurrenceConfig);
        self::assertSame(['scope' => 'whole_purchase'], $older->config);
        self::assertSame(['days' => [6, 0]], $older->recurrenceConfig);
    }

    /**
     * @param  array<string, mixed>  $config
     * @param  array<string, mixed>  $recurrenceConfig
     * @return array<string, mixed>
     */
    private function purchaseCampaign(int $id, array $config, array $recurrenceConfig): array
    {
        return [
            'id' => $id, 'name' => 'Weekend', 'family' => 'purchase', 'moment' => null,
            'config' => $config, 'version' => 1,
            'multiplier' => 2, 'recurrence_type' => 'days_of_week',
            'recurrence_config' => $recurrenceConfig,
            'schedule_summary' => 'Elk weekend', 'starts_at' => '2026-08-01', 'ends_at' => null,
            'status' => ['value' => 'active', 'label' => 'Actief'],
            'min_transaction_amount' => null,
            'created_at' => null, 'updated_at' => null,
        ];
    }

    /**
     * An API from before `extra_amount` sends no such key. The SDK reads 0, which is what the
     * field means: points only. A reward and a redemption from that API must not read as broken.
     */
    public function test_a_reward_and_a_redemption_from_before_the_extra_amount_read_points_only(): void
    {
        $this->fake->queueData([[
            'id' => 1, 'name' => 'Gratis koffie', 'description' => null, 'type' => 'free_product',
            'point_cost' => 100, 'image_url' => null, 'remaining_stock' => 5, 'total_stock' => 10,
            'available_from' => null, 'available_until' => null,
        ]]);
        $this->fake->queueData([
            'redemption_id' => 11, 'confirmation_code' => 'PNTJ-COFFEE01',
            'reward' => ['name' => 'Gratis koffie', 'type' => 'free_product'],
            'points_deducted' => 100, 'remaining_balance' => 0,
            'redeemed_at' => '2026-07-30T10:00:00+00:00', 'expires_at' => null,
            'type_specific_data' => ['product_reference' => 'SKU-COFFEE'],
        ], 201);

        $reward = $this->puntjes()->rewards->list()[0];
        $redemption = $this->puntjes()->redemptions->create(new CreateRedemption('CARD-1', rewardId: 1, idempotencyKey: 'old-1'));

        self::assertSame(0, $reward->extraAmount);
        self::assertSame(0, $redemption->extraAmount());
    }

    public function test_a_statistics_envelope_without_a_loyalty_block_decodes(): void
    {
        $this->fake->queueData([
            'period' => ['preset' => '30d', 'from' => '', 'to' => '', 'timezone' => 'Europe/Brussels', 'granularity' => 'daily'],
            'commerce' => ['orders' => 40, 'revenue_cents' => 168000, 'average_order_value_cents' => 4200, 'itemized' => [], 'volume_trend' => []],
            'loyalty' => null,
        ]);

        $stats = $this->puntjes()->statistics->get(Period::ThirtyDays);

        self::assertNull($stats->loyalty);
        self::assertSame(168000, $stats->commerce->revenueCents);
    }
}
