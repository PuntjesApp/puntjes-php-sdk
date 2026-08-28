<?php

declare(strict_types=1);

namespace Puntjes\Tests\Unit;

use Puntjes\Enum\BranchType;
use Puntjes\Enum\ErrorCode;
use Puntjes\Enum\Period;
use Puntjes\Exception\ApiException;
use Puntjes\Model\Branch;
use Puntjes\Request\CreateRedemption;
use Puntjes\Request\SubmitTransaction;
use Puntjes\Tests\Support\TestCase;

/**
 * Branches, across every surface that carries one.
 *
 * The distinction these tests exist to pin is the three-state one: no branch named at
 * all, the reserved `none` bucket, and one named shop. Collapsing any two of them is
 * how a filtered report and an unfiltered one end up reporting the same number under
 * different labels.
 */
final class BranchTest extends TestCase
{
    /** @return array<string, mixed> */
    private function transactionFixture(mixed $branch): array
    {
        return [
            'id' => 1,
            'customer_id' => 42,
            'idempotency_key' => 'order-77',
            'total_amount' => 4200,
            'description' => null,
            'external_reference' => null,
            'branch' => $branch,
            'created_at' => '2026-08-28T10:00:00+00:00',
            'points_earned' => 42,
            'rules_applied' => [],
            'items' => [],
        ];
    }

    public function test_a_transaction_reports_the_shop_it_was_rung_up_at(): void
    {
        $this->fake->queueData($this->transactionFixture([
            'external_id' => 'centrum',
            'name' => 'Centrum',
            'type' => 'physical',
        ]), 201);

        $transaction = $this->puntjes()->transactions->submit(new SubmitTransaction(
            identifier: 'CARD-1',
            totalAmount: 4200,
            idempotencyKey: 'order-77',
            branch: 'centrum',
        ));

        self::assertSame('centrum', $transaction->branch?->externalId);
        self::assertSame('Centrum', $transaction->branch?->name);
        self::assertSame(BranchType::Physical, $transaction->branch?->type);
        self::assertSame('centrum', $this->fake->bodyAt(1)['branch']);
    }

    public function test_an_unassigned_transaction_reports_a_null_branch(): void
    {
        $this->fake->queueData($this->transactionFixture(null), 201);

        $transaction = $this->puntjes()->transactions->submit(new SubmitTransaction(
            identifier: 'CARD-1',
            totalAmount: 4200,
        ));

        self::assertNull($transaction->branch);
        // Omitted rather than sent as null: the API falls back to the credential's own
        // default branch, and an explicit null would be a key it cannot resolve.
        self::assertArrayNotHasKey('branch', $this->fake->bodyAt(1));
    }

    public function test_a_branch_type_this_sdk_does_not_know_keeps_its_wire_value(): void
    {
        $this->fake->queueData($this->transactionFixture([
            'external_id' => 'popup',
            'name' => 'Zomermarkt',
            'type' => 'market_stall',
        ]), 201);

        $transaction = $this->puntjes()->transactions->submit(new SubmitTransaction(
            identifier: 'CARD-1',
            totalAmount: 100,
        ));

        self::assertNull($transaction->branch?->type);
        self::assertSame('market_stall', $transaction->branch?->rawType);
    }

    public function test_an_unknown_branch_key_is_told_apart_from_a_closed_shop(): void
    {
        $this->fake->queueError(422, 'BRANCH_NOT_FOUND', 'No branch found for this key.');

        try {
            $this->puntjes()->transactions->submit(new SubmitTransaction(
                identifier: 'CARD-1',
                totalAmount: 4200,
                branch: 'centrun',
            ));
            self::fail('Expected the mistyped key to be refused.');
        } catch (ApiException $e) {
            self::assertSame(ErrorCode::BranchNotFound, $e->errorCode());
        }

        $this->fake->queueError(422, 'BRANCH_INACTIVE', 'This branch is no longer active.');

        try {
            $this->puntjes()->transactions->submit(new SubmitTransaction(
                identifier: 'CARD-1',
                totalAmount: 4200,
                branch: 'gesloten',
            ));
            self::fail('Expected the closed shop to be refused.');
        } catch (ApiException $e) {
            self::assertSame(ErrorCode::BranchInactive, $e->errorCode());
        }
    }

    public function test_a_refused_branch_is_never_retried(): void
    {
        // 422 is not a transient failure. Replaying it would only spend the budget, and
        // the transaction was not recorded either way.
        $this->fake->queueError(422, 'BRANCH_INACTIVE', 'This branch is no longer active.');

        try {
            $this->puntjes()->transactions->submit(new SubmitTransaction(
                identifier: 'CARD-1',
                totalAmount: 4200,
                idempotencyKey: 'order-77',
                branch: 'gesloten',
            ));
        } catch (ApiException) {
            // expected
        }

        self::assertSame(1, $this->fake->apiRequestCount());
    }

    public function test_a_redemption_names_where_the_reward_was_handed_over(): void
    {
        $this->fake->queueData([
            'redemption_id' => 11,
            'confirmation_code' => 'PNTJ-ABC123',
            'reward' => ['name' => 'Gratis koffie', 'type' => 'free_product'],
            'points_deducted' => 100,
            'remaining_balance' => 220,
            'redeemed_at' => '2026-08-28T10:00:00+00:00',
            'expires_at' => null,
            'type_specific_data' => [],
        ], 201);

        $this->puntjes()->redemptions->create(new CreateRedemption(
            identifier: 'CARD-1',
            rewardId: 3,
            idempotencyKey: 'redeem-1',
            branch: 'centrum',
        ));

        self::assertSame('centrum', $this->fake->bodyAt(1)['branch']);
    }

    public function test_a_reward_limited_to_branches_refuses_anywhere_else(): void
    {
        $this->fake->queueError(422, 'BRANCH_REQUIRED', 'This reward can only be redeemed at one of the branches it is limited to.');

        try {
            $this->puntjes()->redemptions->create(new CreateRedemption(
                identifier: 'CARD-1',
                rewardId: 3,
                branch: 'webshop',
            ));
            self::fail('Expected the out-of-scope branch to be refused.');
        } catch (ApiException $e) {
            self::assertSame(ErrorCode::BranchRequired, $e->errorCode());
        }
    }

    public function test_a_reward_scope_of_null_means_everywhere_and_an_empty_one_means_nowhere(): void
    {
        $this->fake->queueData([
            [
                'id' => 1, 'name' => 'Gratis koffie', 'description' => null, 'type' => 'free_product',
                'point_cost' => 100, 'image_url' => null, 'remaining_stock' => 5, 'total_stock' => 10,
                'available_from' => null, 'available_until' => null,
                'branches' => null,
            ],
            [
                'id' => 2, 'name' => 'Webshop-korting', 'description' => null, 'type' => 'discount',
                'point_cost' => 200, 'image_url' => null, 'remaining_stock' => 5, 'total_stock' => null,
                'available_from' => null, 'available_until' => null,
                'branches' => [['external_id' => 'webshop', 'name' => 'Webshop', 'type' => 'online']],
            ],
            [
                'id' => 3, 'name' => 'Wees van een gesloten filiaal', 'description' => null, 'type' => 'discount',
                'point_cost' => 300, 'image_url' => null, 'remaining_stock' => 1, 'total_stock' => null,
                'available_from' => null, 'available_until' => null,
                'branches' => [],
            ],
        ]);

        $rewards = $this->puntjes()->rewards->list();

        self::assertNull($rewards[0]->branches);
        self::assertTrue($rewards[0]->isRedeemableEverywhere());

        self::assertSame('webshop', $rewards[1]->branches[0]->externalId ?? null);
        self::assertFalse($rewards[1]->isRedeemableEverywhere());

        // Every branch the scope named has since been deleted: still scoped, and now
        // matching nothing. Reading this as "everywhere" would advertise a reward that
        // can never be redeemed.
        self::assertSame([], $rewards[2]->branches);
        self::assertFalse($rewards[2]->isRedeemableEverywhere());
    }

    public function test_a_campaign_carries_the_shops_it_runs_at(): void
    {
        $this->fake->queuePage([[
            'id' => 1, 'name' => 'Dubbele punten', 'family' => 'purchase', 'moment' => null,
            'config' => ['branch_ids' => [4]], 'version' => 2,
            'multiplier' => 2, 'recurrence_type' => 'weekly',
            'recurrence_config' => ['days' => ['friday']],
            'schedule_summary' => 'Elke vrijdag', 'starts_at' => '2026-08-01', 'ends_at' => null,
            'status' => ['value' => 'active', 'label' => 'Actief'],
            'min_transaction_amount' => 25.0,
            'branches' => [['external_id' => 'centrum', 'name' => 'Centrum', 'type' => 'physical']],
            'created_at' => null, 'updated_at' => null,
        ]]);

        $campaign = $this->puntjes()->campaigns->list()->firstPage()->first();

        self::assertSame('purchase', $campaign?->family);
        self::assertSame(2, $campaign?->version);
        self::assertSame('centrum', $campaign?->branches[0]->externalId ?? null);
        self::assertFalse($campaign?->runsEverywhere());
    }

    public function test_the_campaign_list_filters_by_branch(): void
    {
        $this->fake->queuePage([]);
        $this->puntjes()->campaigns->list(branch: 'centrum')->firstPage();
        self::assertStringContainsString('branch=centrum', $this->fake->uriAt(1));

        $this->fake->queuePage([]);
        $this->puntjes()->campaigns->list()->firstPage();
        self::assertStringNotContainsString('branch=', $this->fake->uriAt(3));
    }

    public function test_statistics_narrow_to_one_shop_and_drop_the_loyalty_block(): void
    {
        $this->fake->queueData([
            'period' => [
                'preset' => '30d', 'from' => '2026-08-01T00:00:00+00:00',
                'to' => '2026-08-28T23:59:59+00:00', 'timezone' => 'Europe/Brussels',
                'granularity' => 'daily',
            ],
            'commerce' => [
                'orders' => 40, 'revenue_cents' => 168000, 'average_order_value_cents' => 4200,
                'itemized' => [], 'volume_trend' => [],
            ],
            // Null under a branch filter: points liability and breakage cannot be
            // attributed to one shop, so there is nothing honest to put here.
            'loyalty' => null,
        ]);

        $stats = $this->puntjes()->statistics->get(Period::ThirtyDays, branch: 'centrum');

        self::assertNull($stats->loyalty);
        self::assertSame(168000, $stats->commerce->revenueCents);
        self::assertStringContainsString('branch=centrum', $this->fake->uriAt(1));
    }

    public function test_the_unassigned_bucket_is_a_filter_word(): void
    {
        $this->fake->queueData([
            'period' => ['preset' => 'today', 'from' => '', 'to' => '', 'timezone' => 'Europe/Brussels', 'granularity' => 'hourly'],
            'commerce' => ['orders' => 0, 'revenue_cents' => 0, 'average_order_value_cents' => 0, 'itemized' => [], 'volume_trend' => []],
            'loyalty' => null,
        ]);

        $this->puntjes()->statistics->get(Period::Today, branch: Branch::UNASSIGNED);

        self::assertSame('none', Branch::UNASSIGNED);
        self::assertStringContainsString('branch=none', $this->fake->uriAt(1));
    }
}
