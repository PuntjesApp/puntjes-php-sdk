<?php

declare(strict_types=1);

namespace Puntjes\Tests\Unit;

use Puntjes\Enum\Period;
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
