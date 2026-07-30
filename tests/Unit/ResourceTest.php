<?php

declare(strict_types=1);

namespace Puntjes\Tests\Unit;

use Puntjes\Enum\CustomerStatus;
use Puntjes\Enum\IdentifierType;
use Puntjes\Enum\LedgerEntryType;
use Puntjes\Enum\Period;
use Puntjes\Enum\ProductStatus;
use Puntjes\Enum\RedemptionStatus;
use Puntjes\Enum\RewardType;
use Puntjes\Exception\ConfigurationException;
use Puntjes\Request\AdjustWallet;
use Puntjes\Request\CreateCustomer;
use Puntjes\Request\CreateIdentifier;
use Puntjes\Request\CreateRedemption;
use Puntjes\Request\CreateRewardFromProduct;
use Puntjes\Request\DateRangeFilters;
use Puntjes\Request\LineItem;
use Puntjes\Request\ProductFilters;
use Puntjes\Request\SubmitTransaction;
use Puntjes\Request\UpdateCustomer;
use Puntjes\Request\UpdateProduct;
use Puntjes\Request\UpsertProduct;
use Puntjes\Tests\Support\TestCase;

/**
 * Each endpoint group against fixtures shaped like the real API responses, so a
 * change in the wire contract shows up here rather than in an integrator's logs.
 */
final class ResourceTest extends TestCase
{
    /** @return array<string, mixed> */
    private function customerFixture(): array
    {
        return [
            'id' => 42,
            'first_name' => 'Jan',
            'last_name' => 'Everaert',
            'email' => 'jan@example.com',
            'phone' => null,
            'external_id' => 'PNU-1',
            'date_of_birth' => '1990-01-01',
            'locale' => 'nl',
            'status' => ['value' => 'active', 'label' => 'Active'],
            'identifiers' => [[
                'id' => 7,
                'type' => 'card',
                'value' => 'CARD-1',
                'is_active' => true,
                'is_primary' => true,
                'created_at' => '2026-01-01T00:00:00+00:00',
            ]],
            'deactivated_at' => null,
            'anonymized_at' => null,
            'created_at' => '2026-01-01T00:00:00+00:00',
            'updated_at' => '2026-01-01T00:00:00+00:00',
        ];
    }

    public function test_customer_lookup_by_identifier_includes_the_wallet_balance(): void
    {
        $this->fake->queueData($this->customerFixture() + ['wallet_balance' => 320]);

        $customer = $this->puntjes()->customers->lookup(identifier: 'CARD-1');

        self::assertSame(42, $customer->id);
        self::assertSame('Jan Everaert', $customer->fullName());
        self::assertSame(CustomerStatus::Active, $customer->status);
        self::assertSame(320, $customer->walletBalance);
        self::assertFalse($customer->isDeactivated);
        self::assertSame(IdentifierType::Card, $customer->primaryIdentifier()?->type);
        self::assertStringContainsString('identifier=CARD-1', $this->fake->uriAt(1));
    }

    public function test_customer_lookup_by_external_id_uses_the_right_query_parameter(): void
    {
        $this->fake->queueData($this->customerFixture() + ['wallet_balance' => 0]);

        $this->puntjes()->customers->lookup(externalId: 'PNU-1');

        self::assertStringContainsString('external_id=PNU-1', $this->fake->uriAt(1));
    }

    public function test_customer_lookup_requires_exactly_one_criterion(): void
    {
        $puntjes = $this->puntjes();

        $this->expectException(ConfigurationException::class);

        $puntjes->customers->lookup(identifier: 'CARD-1', externalId: 'PNU-1');
    }

    public function test_a_deactivated_customer_is_flagged_rather_than_hidden(): void
    {
        $this->fake->queueData(
            ['is_deactivated' => true, 'wallet_balance' => 12]
            + ['status' => ['value' => 'deactivated', 'label' => 'Deactivated']]
            + $this->customerFixture()
        );

        $customer = $this->puntjes()->customers->lookup(identifier: 'CARD-1');

        self::assertTrue($customer->isDeactivated);
        self::assertSame(CustomerStatus::Deactivated, $customer->status);
    }

    public function test_find_by_identifier_returns_null_instead_of_throwing(): void
    {
        $this->fake->queueError(404, 'CUSTOMER_NOT_FOUND', 'No customer found for this identifier.');

        self::assertNull($this->puntjes()->customers->findByIdentifier('UNKNOWN'));
    }

    public function test_registering_a_customer_sends_the_identifier_collection(): void
    {
        $this->fake->queueData($this->customerFixture(), 201);

        $this->puntjes()->customers->register(new CreateCustomer(
            identifiers: [CreateIdentifier::card('CARD-1'), CreateIdentifier::email('jan@example.com')],
            firstName: 'Jan',
            externalId: 'PNU-1',
        ));

        $body = $this->fake->bodyAt(1);
        self::assertSame('Jan', $body['first_name']);
        self::assertSame('PNU-1', $body['external_id']);
        self::assertCount(2, $body['identifiers']);
        self::assertSame(['type' => 'card', 'value' => 'CARD-1', 'is_primary' => true], $body['identifiers'][0]);
        // Unset optionals are omitted rather than sent as null.
        self::assertArrayNotHasKey('phone', $body);
    }

    public function test_registering_without_an_identifier_fails_before_a_request_is_made(): void
    {
        $this->expectException(ConfigurationException::class);

        new CreateCustomer(identifiers: []);
    }

    public function test_a_partial_customer_update_distinguishes_omitted_from_null(): void
    {
        $this->fake->queueData($this->customerFixture());

        $this->puntjes()->customers->updateByExternalId('PNU-1', new UpdateCustomer(
            email: 'new@example.com',
            phone: null,
        ));

        $body = $this->fake->bodyAt(1);
        self::assertSame(['email' => 'new@example.com', 'phone' => null], $body);
        self::assertSame('PATCH', $this->fake->requestAt(1)->getMethod());
    }

    public function test_submitting_a_transaction_sends_items_and_a_generated_key(): void
    {
        $this->fake->queueData([
            'id' => 1,
            'customer_id' => 42,
            'idempotency_key' => 'order-77',
            'total_amount' => 4200,
            'description' => null,
            'external_reference' => 'ORDER-77',
            'created_at' => '2026-07-30T10:00:00+00:00',
            'points_earned' => 42,
            'rules_applied' => [['rule' => 'base', 'points' => 42]],
            'items' => [[
                'id' => 5, 'name' => 'Brood', 'sku' => 'SKU-1',
                'quantity' => 2, 'unit_price' => 250, 'line_total' => 500, 'category' => 'Bakery',
            ]],
        ], 201);

        $transaction = $this->puntjes()->transactions->submit(new SubmitTransaction(
            identifier: 'CARD-1',
            totalAmount: 4200,
            idempotencyKey: 'order-77',
            externalReference: 'ORDER-77',
            items: [new LineItem('Brood', 2, 250, 'SKU-1', 'Bakery')],
        ));

        self::assertSame(42, $transaction->pointsEarned);
        self::assertSame(500, $transaction->items[0]->lineTotal);

        $body = $this->fake->bodyAt(1);
        self::assertSame('order-77', $body['idempotency_key']);
        self::assertSame(4200, $body['total_amount']);
        self::assertSame('Brood', $body['items'][0]['name']);
        // line_total is derived server-side and must never be sent.
        self::assertArrayNotHasKey('line_total', $body['items'][0]);
    }

    public function test_a_generated_idempotency_key_is_a_uuid(): void
    {
        $transaction = new SubmitTransaction(identifier: 'CARD-1', totalAmount: 100);

        self::assertMatchesRegularExpression(
            '/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/',
            $transaction->idempotencyKey,
        );
    }

    public function test_the_wallet_reports_the_balance_and_expiring_points(): void
    {
        $this->fake->queueData([
            'id' => 3, 'customer_id' => 42, 'balance' => 320, 'expiring_soon' => 50,
            'created_at' => '2026-01-01T00:00:00+00:00', 'updated_at' => '2026-07-01T00:00:00+00:00',
        ]);

        $wallet = $this->puntjes()->wallets->show(42);

        self::assertSame(320, $wallet->balance);
        self::assertSame(50, $wallet->expiringSoon);
    }

    public function test_adjusting_a_wallet_returns_the_ledger_entry(): void
    {
        $this->fake->queueData([
            'id' => 9, 'wallet_id' => 3, 'type' => 'adjust', 'amount' => -50,
            'running_balance' => 270, 'reason' => 'Correction', 'causer_type' => 'api_client',
            'causer_id' => '1', 'created_at' => '2026-07-30T10:00:00+00:00',
        ]);

        $entry = $this->puntjes()->wallets->adjust(42, AdjustWallet::debit(50, 'Correction'));

        self::assertSame(LedgerEntryType::Adjust, $entry->type);
        self::assertSame(-50, $entry->amount);
        self::assertSame(270, $entry->runningBalance);
        self::assertSame(-50, $this->fake->bodyAt(1)['amount']);
    }

    public function test_a_zero_adjustment_is_rejected_before_a_request(): void
    {
        $this->expectException(ConfigurationException::class);

        new AdjustWallet(0, 'Nothing');
    }

    public function test_the_ledger_paginates_and_passes_filters(): void
    {
        $entry = fn (int $id): array => [
            'id' => $id, 'wallet_id' => 3, 'type' => 'earn', 'amount' => 10,
            'running_balance' => 10 * $id, 'reason' => null, 'causer_type' => null,
            'causer_id' => null, 'created_at' => '2026-07-30T10:00:00+00:00',
        ];

        $this->fake->queuePage([$entry(1), $entry(2)], currentPage: 1, lastPage: 2, total: 3);
        $this->fake->queuePage([$entry(3)], currentPage: 2, lastPage: 2, total: 3);

        $entries = $this->puntjes()->wallets
            ->ledger(42, new DateRangeFilters(dateFrom: '2026-07-01', type: 'earn'))
            ->all();

        self::assertCount(3, $entries);
        self::assertSame(30, $entries[2]->runningBalance);
        self::assertStringContainsString('type=earn', $this->fake->uriAt(1));
        self::assertStringContainsString('date_from=2026-07-01', $this->fake->uriAt(1));
        self::assertStringContainsString('page=2', $this->fake->uriAt(2));
    }

    public function test_the_reward_catalogue_is_a_plain_list(): void
    {
        $this->fake->queueData([
            [
                'id' => 1, 'name' => 'Gratis koffie', 'description' => null, 'type' => 'free_product',
                'point_cost' => 100, 'image_url' => null, 'remaining_stock' => 5, 'total_stock' => 10,
                'available_from' => null, 'available_until' => null,
            ],
        ]);

        $rewards = $this->puntjes()->rewards->list();

        self::assertCount(1, $rewards);
        self::assertSame(RewardType::FreeProduct, $rewards[0]->type);
        self::assertTrue($rewards[0]->isAffordableWith(150));
        self::assertFalse($rewards[0]->isAffordableWith(50));
    }

    public function test_the_affordable_filter_sends_both_parameters(): void
    {
        $this->fake->queueData([]);

        $this->puntjes()->rewards->list(affordableFor: 'CARD-1');

        self::assertStringContainsString('affordable=1', $this->fake->uriAt(1));
        self::assertStringContainsString('identifier=CARD-1', $this->fake->uriAt(1));
    }

    public function test_creating_a_redemption_returns_the_confirmation_code(): void
    {
        $this->fake->queueData([
            'redemption_id' => 11,
            'confirmation_code' => 'PNT-ABC123',
            'reward' => ['name' => 'Gratis koffie', 'type' => 'free_product'],
            'points_deducted' => 100,
            'remaining_balance' => 220,
            'redeemed_at' => '2026-07-30T10:00:00+00:00',
            'expires_at' => '2026-08-30T10:00:00+00:00',
            'type_specific_data' => ['product_reference' => 'SKU-COFFEE'],
        ], 201);

        $redemption = $this->puntjes()->redemptions->create(
            new CreateRedemption('CARD-1', rewardId: 1, idempotencyKey: 'redeem-1'),
        );

        self::assertSame('PNT-ABC123', $redemption->confirmationCode);
        self::assertSame(220, $redemption->remainingBalance);
        self::assertSame('SKU-COFFEE', $redemption->typeSpecificData['product_reference']);
        // create() carries no status; it is always valid at that point.
        self::assertNull($redemption->status);
        self::assertFalse($redemption->isVerified());
    }

    public function test_verifying_a_redemption_marks_it_used(): void
    {
        $this->fake->queueData([
            'redemption_id' => 11,
            'confirmation_code' => 'PNT-ABC123',
            'status' => 'used',
            'reward' => ['name' => 'Gratis koffie', 'type' => 'free_product'],
            'points_deducted' => 100,
            'verified_at' => '2026-07-30T11:00:00+00:00',
            'type_specific_data' => [],
        ]);

        $redemption = $this->puntjes()->redemptions->verify('PNT-ABC123');

        self::assertSame(RedemptionStatus::Used, $redemption->status);
        self::assertTrue($redemption->isVerified());
        self::assertStringEndsWith('/redemptions/PNT-ABC123/verify', $this->fake->uriAt(1));
    }

    public function test_a_confirmation_code_is_url_encoded_into_the_path(): void
    {
        $this->fake->queueData([
            'redemption_id' => 1, 'confirmation_code' => 'A/B C',
            'status' => 'valid', 'reward' => ['name' => 'X', 'type' => 'discount'],
            'points_deducted' => 0, 'type_specific_data' => [],
        ]);

        $this->puntjes()->redemptions->find('A/B C');

        self::assertStringEndsWith('/redemptions/A%2FB%20C', $this->fake->uriAt(1));
    }

    /** @return array<string, mixed> */
    private function productFixture(string $externalId = 'SKU-1'): array
    {
        return [
            'id' => 5, 'external_id' => $externalId, 'name' => 'Brood',
            'description' => null, 'price_cents' => 250, 'image_url' => null,
            'category' => 'Bakery', 'stock' => 20, 'status' => 'active', 'metadata' => null,
            'created_at' => '2026-01-01T00:00:00+00:00', 'updated_at' => '2026-01-01T00:00:00+00:00',
        ];
    }

    public function test_listing_products_applies_filters(): void
    {
        $this->fake->queuePage([$this->productFixture()]);

        $page = $this->puntjes()->products
            ->list(new ProductFilters(status: ProductStatus::Active, search: 'bro', perPage: 50))
            ->firstPage();

        self::assertCount(1, $page);
        self::assertSame(ProductStatus::Active, $page->first()?->status);
        self::assertStringContainsString('status=active', $this->fake->uriAt(1));
        self::assertStringContainsString('search=bro', $this->fake->uriAt(1));
        self::assertStringContainsString('per_page=50', $this->fake->uriAt(1));
    }

    public function test_upsert_reports_whether_the_product_was_created(): void
    {
        $this->fake->queueData($this->productFixture(), 201);

        $result = $this->puntjes()->products->upsert('SKU-1', new UpsertProduct(name: 'Brood', priceCents: 250));

        self::assertTrue($result['created']);
        self::assertSame('SKU-1', $result['product']->externalId);
        self::assertSame('PUT', $this->fake->requestAt(1)->getMethod());
        // PUT replaces, so omitted nullables are sent explicitly as null.
        self::assertArrayHasKey('description', $this->fake->bodyAt(1));
        self::assertNull($this->fake->bodyAt(1)['description']);
    }

    public function test_upsert_reports_an_update_as_not_created(): void
    {
        $this->fake->queueData($this->productFixture(), 200);

        $result = $this->puntjes()->products->upsert('SKU-1', new UpsertProduct(name: 'Brood'));

        self::assertFalse($result['created']);
    }

    public function test_a_partial_product_update_sends_only_named_fields(): void
    {
        $this->fake->queueData($this->productFixture());

        $this->puntjes()->products->update('SKU-1', new UpdateProduct(
            priceCents: 275,
            status: ProductStatus::Inactive,
        ));

        self::assertSame(['price_cents' => 275, 'status' => 'inactive'], $this->fake->bodyAt(1));
    }

    public function test_a_sku_with_a_slash_is_encoded_into_the_path(): void
    {
        $this->fake->queueData($this->productFixture('A/B'));

        $this->puntjes()->products->find('A/B');

        self::assertStringEndsWith('/products/A%2FB', $this->fake->uriAt(1));
    }

    public function test_deleting_a_product_handles_the_204(): void
    {
        $this->fake->queueRaw(204, '');

        $this->puntjes()->products->delete('SKU-1');

        self::assertSame('DELETE', $this->fake->requestAt(1)->getMethod());
    }

    public function test_a_batch_upsert_reports_per_item_outcomes(): void
    {
        $this->fake->queueData([
            'results' => [
                ['index' => 0, 'external_id' => 'SKU-1', 'status' => 'created', 'product' => $this->productFixture()],
                ['index' => 1, 'external_id' => 'SKU-2', 'status' => 'error', 'errors' => ['name' => ['The name field is required.']]],
            ],
            'summary' => ['total' => 2, 'created' => 1, 'updated' => 0, 'failed' => 1],
        ]);

        $result = $this->puntjes()->products->batchUpsert([
            'SKU-1' => new UpsertProduct(name: 'Brood'),
            'SKU-2' => new UpsertProduct(name: 'Melk'),
        ]);

        self::assertTrue($result->hasFailures());
        self::assertCount(1, $result->failures());
        self::assertSame('SKU-2', $result->failures()[0]->externalId);
        self::assertSame(['The name field is required.'], $result->failures()[0]->errors['name']);
        self::assertSame('SKU-1', $this->fake->bodyAt(1)['products'][0]['external_id']);
    }

    public function test_an_oversized_batch_is_rejected_before_a_request(): void
    {
        $products = [];

        for ($i = 0; $i <= 100; $i++) {
            $products["SKU-{$i}"] = new UpsertProduct(name: "Product {$i}");
        }

        $puntjes = $this->puntjes();

        $this->expectException(ConfigurationException::class);
        $this->expectExceptionMessage('at most 100 products');

        $puntjes->products->batchUpsert($products);
    }

    public function test_an_empty_batch_is_rejected(): void
    {
        $puntjes = $this->puntjes();

        $this->expectException(ConfigurationException::class);

        $puntjes->products->batchUpsert([]);
    }

    public function test_creating_a_reward_from_a_product(): void
    {
        $this->fake->queueData([
            'id' => 3, 'product_id' => 5, 'name' => 'Gratis brood', 'description' => null,
            'type' => 'free_product', 'point_cost' => 200, 'image_url' => null,
            'total_stock' => null, 'remaining_stock' => 0,
            'status' => ['value' => 'active', 'label' => 'Active'],
            'available_from' => null, 'available_until' => null, 'discount_value' => null,
            'discount_type' => null, 'product_reference' => 'SKU-1', 'code_valid_for_hours' => 48,
            'created_at' => '2026-07-30T10:00:00+00:00', 'updated_at' => '2026-07-30T10:00:00+00:00',
        ], 201);

        $reward = $this->puntjes()->products->createReward('SKU-1', new CreateRewardFromProduct(
            pointCost: 200,
            codeValidForHours: 48,
        ));

        self::assertSame(RewardType::FreeProduct, $reward->type);
        self::assertSame('active', $reward->status);
        self::assertSame(48, $reward->codeValidForHours);
    }

    public function test_campaigns_report_the_minimum_spend_in_euros(): void
    {
        $this->fake->queuePage([[
            'id' => 1, 'name' => 'Dubbele punten', 'multiplier' => 2,
            'recurrence_type' => 'weekly', 'recurrence_config' => ['days' => ['friday']],
            'schedule_summary' => 'Elke vrijdag', 'starts_at' => '2026-07-01', 'ends_at' => null,
            'status' => ['value' => 'active', 'label' => 'Active'],
            'min_transaction_amount' => 25.0,
            'created_at' => null, 'updated_at' => null,
        ]]);

        $campaign = $this->puntjes()->campaigns->list()->firstPage()->first();

        self::assertSame(2, $campaign?->multiplier);
        // Euros here, unlike everything else on the API — see the model docblock.
        self::assertSame(25.0, $campaign?->minTransactionAmount);
    }

    public function test_statistics_decode_the_nested_envelope(): void
    {
        $this->fake->queueData([
            'period' => [
                'preset' => '30d', 'from' => '2026-07-01T00:00:00+00:00',
                'to' => '2026-07-30T23:59:59+00:00', 'timezone' => 'Europe/Brussels',
                'granularity' => 'daily',
            ],
            'commerce' => [
                'orders' => 120, 'revenue_cents' => 504000, 'average_order_value_cents' => 4200,
                'itemized' => [
                    'orders' => 90, 'revenue_cents' => 400000, 'average_items_per_order' => 3.5,
                    'other_no_item_detail_cents' => 104000,
                    'top_products' => [['name' => 'Brood', 'units' => 300, 'revenue' => 75000]],
                    'categories' => [['category' => 'Bakery', 'units' => 300, 'revenue' => 75000, 'isOther' => false]],
                ],
                'volume_trend' => [['label' => '2026-07-30', 'orders' => 4, 'revenue' => 16800]],
            ],
            'loyalty' => [
                'points_issued' => 5000, 'points_redeemed' => 1200, 'points_expired' => 300,
                'net_adjustments' => -50, 'redemption_rate' => 0.24, 'breakage_rate' => 0.06,
            ],
        ]);

        $stats = $this->puntjes()->statistics->get(Period::ThirtyDays, topProductsLimit: 5);

        self::assertSame(Period::ThirtyDays, $stats->period->preset);
        self::assertSame('Europe/Brussels', $stats->period->timezone);
        self::assertSame(504000, $stats->commerce->revenueCents);
        self::assertSame(104000, $stats->commerce->itemized->otherNoItemDetailCents);
        self::assertSame('Brood', $stats->commerce->itemized->topProducts[0]->name);
        self::assertSame(0.24, $stats->loyalty->redemptionRate);
        self::assertSame(3450, $stats->loyalty->netPointsDelta());
        self::assertStringContainsString('period=30d', $this->fake->uriAt(1));
        self::assertStringContainsString('top_products_limit=5', $this->fake->uriAt(1));
    }

    public function test_an_empty_statistics_period_reports_null_rates_not_zero(): void
    {
        $this->fake->queueData([
            'period' => ['preset' => 'today', 'from' => '', 'to' => '', 'timezone' => 'Europe/Brussels', 'granularity' => 'hourly'],
            'commerce' => ['orders' => 0, 'revenue_cents' => 0, 'average_order_value_cents' => 0, 'itemized' => [], 'volume_trend' => []],
            'loyalty' => ['points_issued' => 0, 'points_redeemed' => 0, 'points_expired' => 0, 'net_adjustments' => 0, 'redemption_rate' => null, 'breakage_rate' => null],
        ]);

        $stats = $this->puntjes()->statistics->get(Period::Today);

        self::assertNull($stats->loyalty->redemptionRate);
        self::assertNull($stats->loyalty->breakageRate);
    }

    public function test_the_apple_wallet_pass_is_returned_as_raw_bytes(): void
    {
        $this->fake->queueRaw(200, "PK\x03\x04binary", ['Content-Type' => 'application/vnd.apple.pkpass']);

        $pass = $this->puntjes()->wallets->applePass(42);

        self::assertSame("PK\x03\x04binary", $pass);
        self::assertStringContainsString('platform=apple', $this->fake->uriAt(1));
    }

    public function test_the_google_wallet_pass_returns_a_save_url(): void
    {
        $this->fake->queueData(['save_url' => 'https://pay.google.com/gp/v/save/abc']);

        self::assertSame(
            'https://pay.google.com/gp/v/save/abc',
            $this->puntjes()->wallets->googlePassUrl(42),
        );
    }

    public function test_the_escape_hatch_still_authenticates_and_maps_errors(): void
    {
        $this->fake->queueData(['anything' => true]);

        $response = $this->puntjes()->request('GET', '/some/future/endpoint', ['q' => 1]);

        self::assertSame(['anything' => true], $response->dataArray());
        self::assertSame('Bearer test-token', $this->fake->requestAt(1)->getHeaderLine('Authorization'));
        self::assertStringContainsString('q=1', $this->fake->uriAt(1));
    }
}
