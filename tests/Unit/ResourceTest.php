<?php

declare(strict_types=1);

namespace Puntjes\Tests\Unit;

use Puntjes\Enum\CustomerStatus;
use Puntjes\Enum\ErrorCode;
use Puntjes\Enum\IdentifierType;
use Puntjes\Enum\LedgerEntryType;
use Puntjes\Enum\Period;
use Puntjes\Enum\ProductStatus;
use Puntjes\Enum\RedemptionStatus;
use Puntjes\Enum\RewardType;
use Puntjes\Enum\VoucherStatus;
use Puntjes\Exception\ApiException;
use Puntjes\Exception\ConfigurationException;
use Puntjes\Exception\ConflictException;
use Puntjes\Exception\NotFoundException;
use Puntjes\Exception\ServerException;
use Puntjes\Exception\TransportException;
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
                'type' => 'loyalty_card',
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
        self::assertSame(IdentifierType::LoyaltyCard, $customer->primaryIdentifier()?->type);
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
            identifiers: [CreateIdentifier::loyaltyCard('7KQ4M2XP'), CreateIdentifier::email('jan@example.com')],
            firstName: 'Jan',
            externalId: 'PNU-1',
        ));

        $body = $this->fake->bodyAt(1);
        self::assertSame('Jan', $body['first_name']);
        self::assertSame('PNU-1', $body['external_id']);
        self::assertCount(2, $body['identifiers']);
        self::assertSame(['type' => 'loyalty_card', 'value' => '7KQ4M2XP', 'is_primary' => true], $body['identifiers'][0]);
        // Unset optionals are omitted rather than sent as null.
        self::assertArrayNotHasKey('phone', $body);
    }

    /**
     * The common case: send no identifiers and the API mints the loyalty card itself. The
     * SDK used to refuse this before a request was ever made.
     */
    public function test_registering_without_identifiers_lets_puntjes_issue_the_card(): void
    {
        $this->fake->queueData($this->customerFixture(), 201);

        $this->puntjes()->customers->register(new CreateCustomer(firstName: 'Jan'));

        $body = $this->fake->bodyAt(1);
        self::assertSame([], $body['identifiers']);
        self::assertSame('Jan', $body['first_name']);
    }

    public function test_a_loyalty_card_identifier_is_sent_with_the_accepted_type(): void
    {
        $this->fake->queueData($this->customerFixture(), 201);

        $this->puntjes()->customers->register(new CreateCustomer(
            identifiers: [CreateIdentifier::loyaltyCard('7KQ4M2XP')],
        ));

        self::assertSame(
            ['type' => 'loyalty_card', 'value' => '7KQ4M2XP', 'is_primary' => true],
            $this->fake->bodyAt(1)['identifiers'][0],
        );
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

    public function test_adjusting_a_merged_customer_is_a_domain_refusal_and_never_retried(): void
    {
        $this->fake->queueError(422, 'CUSTOMER_DEACTIVATED', 'This customer was merged into another account.');

        try {
            $this->puntjes()->wallets->adjust(42, AdjustWallet::credit(100, 'Goodwill', 'adjust-1'));
            self::fail('Expected an ApiException.');
        } catch (ApiException $e) {
            self::assertSame(ApiException::class, $e::class);
            self::assertTrue($e->is(ErrorCode::CustomerDeactivated));
            self::assertSame(422, $e->status());
        }

        self::assertSame(1, $this->fake->apiRequestCount());
    }

    /**
     * @return array<string, array{string}>
     */
    public static function walletPassProvider(): array
    {
        return [
            'apple' => ['applePass'],
            'google' => ['googlePassUrl'],
        ];
    }

    /**
     * @dataProvider walletPassProvider
     */
    public function test_a_wallet_pass_for_a_merged_customer_is_a_domain_refusal(string $method): void
    {
        $this->fake->queueError(422, 'CUSTOMER_DEACTIVATED', 'This customer was merged into another account.');

        try {
            $this->puntjes()->wallets->{$method}(42);
            self::fail('Expected an ApiException.');
        } catch (ApiException $e) {
            self::assertSame(ApiException::class, $e::class);
            self::assertTrue($e->is(ErrorCode::CustomerDeactivated));
            self::assertSame(422, $e->status());
        }

        self::assertSame(1, $this->fake->apiRequestCount());
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

    public function test_the_reward_catalogue_names_the_payment_amount_the_till_collects(): void
    {
        $this->fake->queueData([
            [
                'id' => 2, 'name' => 'Steak', 'description' => null, 'type' => 'free_product',
                'point_cost' => 500, 'image_url' => null, 'remaining_stock' => 5, 'total_stock' => null,
                'available_from' => null, 'available_until' => null, 'payment_amount' => 1999,
            ],
        ]);

        $rewards = $this->puntjes()->rewards->list();

        self::assertSame(1999, $rewards[0]->paymentAmount);
    }

    public function test_the_reward_catalogue_names_the_product_a_reward_is_about(): void
    {
        $item = static fn (int $id, string $type, ?string $reference): array => [
            'id' => $id, 'name' => 'Reward '.$id, 'description' => null, 'type' => $type,
            'point_cost' => 100, 'image_url' => null, 'remaining_stock' => 5, 'total_stock' => null,
            'available_from' => null, 'available_until' => null, 'payment_amount' => 0,
            'product_reference' => $reference,
        ];
        $older = $item(4, 'discount', null);
        unset($older['product_reference']);
        $this->fake->queueData([
            $item(1, 'discount', 'KT-10234'),
            $item(2, 'discount', null),
            $item(3, 'free_product', 'STEAK-01'),
            $older,
        ]);

        $rewards = $this->puntjes()->rewards->list();

        self::assertSame('KT-10234', $rewards[0]->productReference);
        self::assertTrue($rewards[0]->isDiscountOnOneProduct());
        self::assertNull($rewards[1]->productReference);
        self::assertFalse($rewards[1]->isDiscountOnOneProduct());
        self::assertSame('STEAK-01', $rewards[2]->productReference);
        self::assertFalse($rewards[2]->isDiscountOnOneProduct());
        self::assertNull($rewards[3]->productReference);
    }

    public function test_a_redemption_tells_the_till_the_payment_amount_to_collect(): void
    {
        $this->fake->queueData($this->redemptionWith(['product_reference' => 'STEAK-01', 'payment_amount' => 200]), 201);
        $this->fake->queueData($this->redemptionWith(['discount_value' => 500, 'discount_type' => 'fixed_amount']), 201);

        $steak = $this->puntjes()->redemptions->create(new CreateRedemption('CARD-1', rewardId: 2, idempotencyKey: 'steak-1'));
        $discount = $this->puntjes()->redemptions->create(new CreateRedemption('CARD-1', rewardId: 3, idempotencyKey: 'disc-1'));

        self::assertSame(200, $steak->paymentAmount());
        self::assertSame(0, $discount->paymentAmount());
    }

    public function test_a_discount_redemption_names_the_one_product_it_is_for(): void
    {
        $this->fake->queueData($this->redemptionWith(['discount_value' => 20, 'discount_type' => 'percentage', 'product_reference' => 'KT-10234'], 'discount'), 201);
        $this->fake->queueData($this->redemptionWith(['discount_value' => 500, 'discount_type' => 'fixed_amount', 'product_reference' => null], 'discount'), 201);
        $this->fake->queueData($this->redemptionWith(['discount_value' => 500, 'discount_type' => 'fixed_amount'], 'discount'), 201);

        $onOneProduct = $this->puntjes()->redemptions->create(new CreateRedemption('CARD-1', rewardId: 4, idempotencyKey: 'kt-1'));
        $wholePurchase = $this->puntjes()->redemptions->create(new CreateRedemption('CARD-1', rewardId: 3, idempotencyKey: 'disc-2'));
        $olderPuntjes = $this->puntjes()->redemptions->create(new CreateRedemption('CARD-1', rewardId: 3, idempotencyKey: 'disc-3'));

        self::assertSame('KT-10234', $onOneProduct->productReference());
        self::assertNull($wholePurchase->productReference());
        self::assertNull($olderPuntjes->productReference());
    }

    public function test_a_voucher_discount_names_the_one_product_it_is_for(): void
    {
        $this->fake->queueData([
            'voucher_code' => 'BON-KT',
            'discount' => ['kind' => 'percentage', 'percentage' => 20, 'product_reference' => 'KT-10234'],
            'valid_until' => null,
            'consumed_at' => '2026-10-06T10:00:00+00:00',
            'campaign_id' => 4,
            'kind' => 'discount',
            'products' => null,
        ]);
        $this->fake->queueData([
            'voucher_code' => 'BON-ALL',
            'discount' => ['kind' => 'fixed', 'amount_cents' => 750, 'product_reference' => null],
            'valid_until' => null,
            'consumed_at' => '2026-10-06T10:00:00+00:00',
            'campaign_id' => 4,
            'kind' => 'discount',
            'products' => null,
        ]);

        $onOneProduct = $this->puntjes()->vouchers->verify('BON-KT');
        $wholePurchase = $this->puntjes()->vouchers->verify('BON-ALL');

        self::assertSame('KT-10234', $onOneProduct->discount?->productReference);
        self::assertTrue($onOneProduct->discount?->isOnOneProduct());
        self::assertSame(300, $onOneProduct->discount?->appliedTo(1500));
        self::assertNull($wholePurchase->discount?->productReference);
        self::assertFalse($wholePurchase->discount?->isOnOneProduct());
    }

    public function test_a_voucher_from_an_older_puntjes_reads_as_a_discount_on_the_whole_purchase(): void
    {
        $this->fake->queueData([
            'voucher_code' => 'BON-OLD',
            'discount' => ['kind' => 'fixed', 'amount_cents' => 750],
            'valid_until' => null,
            'consumed_at' => '2026-10-06T10:00:00+00:00',
            'campaign_id' => 4,
            'kind' => 'discount',
            'products' => null,
        ]);

        $result = $this->puntjes()->vouchers->verify('BON-OLD');

        self::assertNull($result->discount?->productReference);
        self::assertFalse($result->discount?->isOnOneProduct());
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

    public function test_verifying_a_cancelled_redemption_is_refused_and_never_retried(): void
    {
        $this->fake->queueError(422, 'CODE_CANCELLED', 'The shop cancelled this redemption.');

        try {
            $this->puntjes()->redemptions->verify('PNT-ABC123');
            self::fail('Expected the cancelled code to be refused.');
        } catch (ApiException $e) {
            self::assertSame(ErrorCode::CodeCancelled, $e->errorCode());
            self::assertSame(422, $e->status());
        }

        self::assertSame(1, $this->fake->apiRequestCount());
    }

    public function test_a_cancelled_redemption_reads_as_cancelled(): void
    {
        $this->fake->queueData([
            'redemption_id' => 11,
            'confirmation_code' => 'PNT-ABC123',
            'status' => 'cancelled',
            'reward' => ['name' => 'Gratis koffie', 'type' => 'free_product'],
            'customer' => ['name' => 'Jan Everaert'],
            'points_deducted' => 100,
            'redeemed_at' => '2026-09-30T10:00:00+00:00',
            'verified_at' => null,
            'expires_at' => null,
            'type_specific_data' => [],
        ]);

        $redemption = $this->puntjes()->redemptions->find('PNT-ABC123');

        self::assertSame(RedemptionStatus::Cancelled, $redemption->status);
        self::assertFalse($redemption->status->isRedeemable());
        self::assertFalse($redemption->isVerified());
    }

    public function test_a_redemption_cancelled_after_the_till_verified_it_stays_verified(): void
    {
        $this->fake->queueData([
            'redemption_id' => 11,
            'confirmation_code' => 'PNT-ABC123',
            'status' => 'cancelled',
            'reward' => ['name' => 'Gratis koffie', 'type' => 'free_product'],
            'customer' => ['name' => 'Jan Everaert'],
            'points_deducted' => 100,
            'redeemed_at' => '2026-09-30T10:00:00+00:00',
            'verified_at' => '2026-09-30T11:00:00+00:00',
            'expires_at' => null,
            'type_specific_data' => [],
        ]);

        $redemption = $this->puntjes()->redemptions->find('PNT-ABC123');

        self::assertSame(RedemptionStatus::Cancelled, $redemption->status);
        self::assertTrue($redemption->isVerified());
    }

    public function test_only_a_valid_redemption_is_redeemable(): void
    {
        self::assertTrue(RedemptionStatus::Valid->isRedeemable());
        self::assertFalse(RedemptionStatus::Used->isRedeemable());
        self::assertFalse(RedemptionStatus::Expired->isRedeemable());
        self::assertFalse(RedemptionStatus::Cancelled->isRedeemable());
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

    public function test_a_customers_redemptions_paginate_and_pass_the_status_filter(): void
    {
        $redemption = fn (int $id, string $status): array => [
            'redemption_id' => $id,
            'confirmation_code' => 'PNTJ-0000000'.$id,
            'status' => $status,
            'reward' => ['name' => 'Gratis koffie', 'type' => 'free_product'],
            'customer' => ['name' => 'Jan Everaert'],
            'points_deducted' => 100,
            'redeemed_at' => '2026-09-30T10:00:00+00:00',
            'verified_at' => null,
            'expires_at' => null,
            'type_specific_data' => ['product_reference' => 'SKU-COFFEE'],
        ];

        $this->fake->queuePage([$redemption(2, 'valid')], currentPage: 1, lastPage: 2, total: 2);
        $this->fake->queuePage([$redemption(1, 'valid')], currentPage: 2, lastPage: 2, total: 2);

        $open = $this->puntjes()->redemptions
            ->forCustomer(42, RedemptionStatus::Valid)
            ->all();

        self::assertCount(2, $open);
        self::assertSame('PNTJ-00000002', $open[0]->confirmationCode);
        self::assertSame(RedemptionStatus::Valid, $open[1]->status);
        self::assertSame('Jan Everaert', $open[1]->customerName);
        self::assertStringContainsString('/customers/42/redemptions', $this->fake->uriAt(1));
        self::assertStringContainsString('status=valid', $this->fake->uriAt(1));
        self::assertStringContainsString('page=2', $this->fake->uriAt(2));
    }

    public function test_a_customers_redemptions_without_a_status_send_no_filter(): void
    {
        $this->fake->queuePage([], currentPage: 1, lastPage: 1, total: 0);

        $this->puntjes()->redemptions->forCustomer(42)->all();

        self::assertStringContainsString('/customers/42/redemptions', $this->fake->uriAt(1));
        self::assertStringNotContainsString('status=', $this->fake->uriAt(1));
    }

    /**
     * @param  array<string, mixed>  $typeSpecificData
     * @return array<string, mixed>
     */
    private function redemptionWith(array $typeSpecificData, string $type = 'free_product'): array
    {
        return [
            'redemption_id' => 12, 'confirmation_code' => 'PNTJ-STEAK001',
            'reward' => ['name' => 'Steak', 'type' => $type],
            'points_deducted' => 500, 'remaining_balance' => 0,
            'redeemed_at' => '2026-10-01T10:00:00+00:00', 'expires_at' => null,
            'type_specific_data' => $typeSpecificData,
        ];
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

    public function test_a_reward_from_a_product_sends_and_reads_the_payment_amount_in_cents(): void
    {
        $this->fake->queueData([
            'id' => 4, 'product_id' => 6, 'name' => 'Steak', 'description' => null,
            'type' => 'free_product', 'point_cost' => 500, 'image_url' => null,
            'total_stock' => null, 'remaining_stock' => 0,
            'status' => ['value' => 'active', 'label' => 'Active'],
            'available_from' => null, 'available_until' => null, 'discount_value' => null,
            'discount_type' => null, 'product_reference' => 'STEAK-01', 'payment_amount' => 1999,
            'code_valid_for_hours' => null,
            'created_at' => '2026-10-01T10:00:00+00:00', 'updated_at' => '2026-10-01T10:00:00+00:00',
        ], 201);

        $reward = $this->puntjes()->products->createReward('STEAK-01', new CreateRewardFromProduct(
            pointCost: 500,
            paymentAmount: 1999,
        ));

        self::assertSame(1999, $this->fake->bodyAt(1)['payment_amount']);
        self::assertSame(1999, $reward->paymentAmount);
    }

    public function test_a_reward_from_a_product_without_an_payment_amount_sends_none(): void
    {
        self::assertArrayNotHasKey('payment_amount', (new CreateRewardFromProduct(pointCost: 200))->toArray());
        self::assertSame(0, (new CreateRewardFromProduct(pointCost: 200, paymentAmount: 0))->toArray()['payment_amount']);
    }

    public function test_the_reward_catalogue_names_each_discounts_kind_and_size_before_the_redemption(): void
    {
        $item = static fn (int $id, string $type, ?string $discountType, ?int $discountValue): array => [
            'id' => $id, 'name' => 'Reward '.$id, 'description' => null, 'type' => $type,
            'point_cost' => 100, 'payment_amount' => 0, 'image_url' => null,
            'remaining_stock' => 5, 'total_stock' => 10,
            'available_from' => null, 'available_until' => null, 'branches' => null,
            'product_reference' => null, 'is_unlimited' => false,
            'discount_type' => $discountType, 'discount_value' => $discountValue,
        ];
        $this->fake->queueData([
            $item(1, 'discount', 'percentage', 15),
            $item(2, 'discount', 'fixed_amount', 500),
            $item(3, 'free_product', null, null),
        ]);

        [$percentage, $fixed, $freeProduct] = $this->puntjes()->rewards->list();

        self::assertSame('percentage', $percentage->discountType);
        self::assertSame(15, $percentage->discountValue);
        self::assertTrue($percentage->isPercentageDiscount());
        self::assertFalse($percentage->isFixedAmountDiscount());

        self::assertSame('fixed_amount', $fixed->discountType);
        self::assertSame(500, $fixed->discountValue);
        self::assertFalse($fixed->isPercentageDiscount());
        self::assertTrue($fixed->isFixedAmountDiscount());

        self::assertNull($freeProduct->discountType);
        self::assertNull($freeProduct->discountValue);
        self::assertFalse($freeProduct->isPercentageDiscount());
        self::assertFalse($freeProduct->isFixedAmountDiscount());
    }

    public function test_a_catalogue_from_an_older_puntjes_leaves_the_discount_kind_unknown(): void
    {
        $this->fake->queueData([[
            'id' => 1, 'name' => 'Korting', 'description' => null, 'type' => 'discount',
            'point_cost' => 100, 'payment_amount' => 0, 'image_url' => null,
            'remaining_stock' => 5, 'total_stock' => 10,
            'available_from' => null, 'available_until' => null, 'branches' => null,
            'product_reference' => null, 'is_unlimited' => false,
        ]]);

        [$reward] = $this->puntjes()->rewards->list();

        self::assertNull($reward->discountType);
        self::assertNull($reward->discountValue);
        self::assertFalse($reward->isPercentageDiscount());
        self::assertFalse($reward->isFixedAmountDiscount());
    }

    public function test_the_reward_catalogue_names_each_rewards_limit_per_customer_and_how_often_the_customer_redeemed_it(): void
    {
        $item = static fn (int $id, ?int $max, ?int $count): array => [
            'id' => $id, 'name' => 'Reward '.$id, 'description' => null, 'type' => 'free_product',
            'point_cost' => 100, 'payment_amount' => 0, 'image_url' => null,
            'remaining_stock' => 5, 'total_stock' => 10,
            'available_from' => null, 'available_until' => null, 'branches' => null,
            'product_reference' => null, 'is_unlimited' => false,
            'discount_type' => null, 'discount_value' => null,
            'max_redemptions_per_customer' => $max, 'customer_redemptions' => $count,
        ];
        $this->fake->queueData([
            $item(1, 3, 1),
            $item(2, 2, 2),
            $item(3, 2, 5),
            $item(4, null, 4),
            $item(5, 3, null),
        ]);

        [$oneUsed, $allUsed, $overUsed, $noLimit, $unknownCustomer] = $this->puntjes()->rewards->list(countRedemptionsFor: 'CARD-1');

        self::assertSame(3, $oneUsed->maxRedemptionsPerCustomer);
        self::assertSame(1, $oneUsed->customerRedemptions);
        self::assertSame(2, $oneUsed->redemptionsLeft());

        self::assertSame(0, $allUsed->redemptionsLeft());
        self::assertSame(0, $overUsed->redemptionsLeft());

        self::assertNull($noLimit->maxRedemptionsPerCustomer);
        self::assertSame(4, $noLimit->customerRedemptions);
        self::assertNull($noLimit->redemptionsLeft());

        self::assertSame(3, $unknownCustomer->maxRedemptionsPerCustomer);
        self::assertNull($unknownCustomer->customerRedemptions);
        self::assertNull($unknownCustomer->redemptionsLeft());
    }

    public function test_a_catalogue_from_an_older_puntjes_has_no_limit_per_customer(): void
    {
        $this->fake->queueData([[
            'id' => 1, 'name' => 'Gratis koffie', 'description' => null, 'type' => 'free_product',
            'point_cost' => 100, 'payment_amount' => 0, 'image_url' => null,
            'remaining_stock' => 5, 'total_stock' => 10,
            'available_from' => null, 'available_until' => null, 'branches' => null,
            'product_reference' => null, 'is_unlimited' => false,
        ]]);

        [$reward] = $this->puntjes()->rewards->list();

        self::assertNull($reward->maxRedemptionsPerCustomer);
        self::assertNull($reward->customerRedemptions);
        self::assertNull($reward->redemptionsLeft());
    }

    public function test_the_catalogue_counts_a_customers_redemptions_without_the_affordable_filter(): void
    {
        $this->fake->queueData([]);

        $this->puntjes()->rewards->list(countRedemptionsFor: 'CARD-1');

        self::assertStringContainsString('identifier=CARD-1', $this->fake->uriAt(1));
        self::assertStringNotContainsString('affordable', $this->fake->uriAt(1));
    }

    public function test_the_affordable_filter_and_the_redemption_count_name_one_customer(): void
    {
        $this->fake->queueData([]);

        $this->puntjes()->rewards->list(affordableFor: 'CARD-1', countRedemptionsFor: 'CARD-1');

        self::assertStringContainsString('affordable=1', $this->fake->uriAt(1));
        self::assertStringContainsString('identifier=CARD-1', $this->fake->uriAt(1));

        try {
            $this->puntjes()->rewards->list(affordableFor: 'CARD-1', countRedemptionsFor: 'CARD-2');
            self::fail('Expected a ConfigurationException.');
        } catch (ConfigurationException $exception) {
            self::assertStringContainsString('affordableFor and countRedemptionsFor differ', $exception->getMessage());
            self::assertSame(1, $this->fake->apiRequestCount());
        }
    }

    public function test_a_reward_from_a_product_sends_and_reads_the_limit_per_customer(): void
    {
        $this->fake->queueData([
            'id' => 4, 'product_id' => 6, 'name' => 'Koffie', 'description' => null,
            'type' => 'free_product', 'point_cost' => 150, 'image_url' => null,
            'total_stock' => 50, 'remaining_stock' => 50,
            'status' => ['value' => 'active', 'label' => 'Active'],
            'available_from' => null, 'available_until' => null, 'discount_value' => null,
            'discount_type' => null, 'product_reference' => 'SKU-1001', 'payment_amount' => 0,
            'code_valid_for_hours' => null,
            'created_at' => '2026-10-01T10:00:00+00:00', 'updated_at' => '2026-10-01T10:00:00+00:00',
            'is_unlimited' => false, 'max_redemptions_per_customer' => 2,
        ], 201);

        $reward = $this->puntjes()->products->createReward('SKU-1001', new CreateRewardFromProduct(
            pointCost: 150,
            maxRedemptionsPerCustomer: 2,
        ));

        self::assertSame(2, $this->fake->bodyAt(1)['max_redemptions_per_customer']);
        self::assertSame(2, $reward->maxRedemptionsPerCustomer);
        self::assertArrayNotHasKey('max_redemptions_per_customer', (new CreateRewardFromProduct(pointCost: 150))->toArray());
    }

    public function test_a_redemption_past_the_limit_per_customer_is_refused_once_and_never_replayed(): void
    {
        $this->fake->queueError(
            422,
            'REDEMPTION_LIMIT_REACHED',
            'This customer already redeemed this reward the most times the shop allows.',
        );

        try {
            $this->puntjes()->redemptions->create(new CreateRedemption('CARD-1', rewardId: 3, idempotencyKey: 'sale-9'));
            self::fail('Expected an ApiException.');
        } catch (ApiException $e) {
            self::assertSame(ApiException::class, $e::class);
            self::assertSame(ErrorCode::RedemptionLimitReached, $e->errorCode());
            self::assertSame(422, $e->status());
        }

        self::assertSame(1, $this->fake->apiRequestCount());
    }

    public function test_the_reward_catalogue_says_which_rewards_have_no_stock_limit(): void
    {
        $item = static fn (int $id, ?int $total, int $remaining, bool $unlimited): array => [
            'id' => $id, 'name' => 'Reward '.$id, 'description' => null, 'type' => 'free_product',
            'point_cost' => 100, 'payment_amount' => 0, 'image_url' => null,
            'remaining_stock' => $remaining, 'total_stock' => $total,
            'available_from' => null, 'available_until' => null, 'branches' => null,
            'product_reference' => null, 'is_unlimited' => $unlimited,
        ];
        $this->fake->queueData([$item(1, null, 0, true), $item(2, 10, 4, false)]);

        $rewards = $this->puntjes()->rewards->list();

        self::assertTrue($rewards[0]->isUnlimited);
        self::assertSame(0, $rewards[0]->remainingStock);
        self::assertFalse($rewards[1]->isUnlimited);
        self::assertSame(4, $rewards[1]->remainingStock);
    }

    public function test_a_reward_from_a_product_reads_is_unlimited(): void
    {
        $this->fake->queueData($this->rewardFixture(['is_unlimited' => true]), 201);
        $this->fake->queueData($this->rewardFixture(['total_stock' => 25, 'remaining_stock' => 25, 'is_unlimited' => false]), 201);

        $unlimited = $this->puntjes()->products->createReward('SKU-1', new CreateRewardFromProduct(pointCost: 200));
        $limited = $this->puntjes()->products->createReward('SKU-1', new CreateRewardFromProduct(pointCost: 200, totalStock: 25));

        self::assertTrue($unlimited->isUnlimited);
        self::assertFalse($limited->isUnlimited);
    }

    public function test_a_reward_from_a_product_sends_the_idempotency_key_only_when_given(): void
    {
        $this->fake->queueData($this->rewardFixture(), 201);

        $this->puntjes()->products->createReward('SKU-1', new CreateRewardFromProduct(
            pointCost: 200,
            idempotencyKey: 'reward-sku-1',
        ));

        self::assertSame('reward-sku-1', $this->fake->bodyAt(1)['idempotency_key']);
        self::assertArrayNotHasKey('idempotency_key', (new CreateRewardFromProduct(pointCost: 200))->toArray());
    }

    public function test_a_reward_retry_with_the_same_key_on_another_amount_is_an_idempotency_conflict(): void
    {
        $this->fake->queueError(
            422,
            'IDEMPOTENCY_KEY_CONFLICT',
            'This idempotency_key was already used for another product or another amount, or its reward was deleted.',
        );

        try {
            $this->puntjes()->products->createReward('SKU-1', new CreateRewardFromProduct(
                pointCost: 300,
                idempotencyKey: 'reward-sku-1',
            ));
            self::fail('Expected an ApiException.');
        } catch (ApiException $e) {
            self::assertSame(ApiException::class, $e::class);
            self::assertTrue($e->is(ErrorCode::IdempotencyKeyConflict));
        }

        self::assertSame(1, $this->fake->apiRequestCount());
    }

    public function test_a_deactivated_customer_is_shown_by_id_with_the_flag_set(): void
    {
        $this->fake->queueData(
            ['is_deactivated' => true, 'deactivated_at' => '2026-10-01T10:00:00+00:00']
            + ['status' => ['value' => 'deactivated', 'label' => 'Deactivated']]
            + $this->customerFixture()
        );
        $this->fake->queueData($this->customerFixture());

        $deactivated = $this->puntjes()->customers->find(9);
        $active = $this->puntjes()->customers->find(9);

        self::assertTrue($deactivated->isDeactivated);
        self::assertSame(CustomerStatus::Deactivated, $deactivated->status);
        self::assertNull($deactivated->walletBalance);
        self::assertFalse($active->isDeactivated);
    }

    public function test_send_card_for_a_deactivated_customer_is_a_domain_refusal(): void
    {
        $this->fake->queueError(422, 'CUSTOMER_DEACTIVATED', 'This customer is deactivated.');

        try {
            $this->puntjes()->customers->sendCard(9);
            self::fail('Expected an ApiException.');
        } catch (ApiException $e) {
            self::assertSame(ApiException::class, $e::class);
            self::assertTrue($e->is(ErrorCode::CustomerDeactivated));
            self::assertSame(422, $e->status());
        }

        self::assertSame(1, $this->fake->apiRequestCount());
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function rewardFixture(array $overrides = []): array
    {
        return $overrides + [
            'id' => 3, 'product_id' => 5, 'name' => 'Gratis brood', 'description' => null,
            'type' => 'free_product', 'point_cost' => 200, 'image_url' => null,
            'total_stock' => null, 'remaining_stock' => 0,
            'status' => ['value' => 'active', 'label' => 'Active'],
            'available_from' => null, 'available_until' => null, 'discount_value' => null,
            'discount_type' => null, 'product_reference' => 'SKU-1', 'payment_amount' => 0,
            'code_valid_for_hours' => null,
            'created_at' => '2026-10-07T10:00:00+00:00', 'updated_at' => '2026-10-07T10:00:00+00:00',
            'is_unlimited' => true,
        ];
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

    public function test_the_loyalty_block_reads_the_points_an_import_brought_along_apart(): void
    {
        $this->fake->queueData([
            'period' => ['preset' => '30d', 'from' => '', 'to' => '', 'timezone' => 'Europe/Brussels', 'granularity' => 'daily'],
            'commerce' => ['orders' => 0, 'revenue_cents' => 0, 'average_order_value_cents' => 0, 'itemized' => [], 'volume_trend' => []],
            'loyalty' => [
                'points_issued' => 600, 'points_redeemed' => 2000, 'points_expired' => 500,
                'net_adjustments' => 0, 'redemption_rate' => 3.333, 'breakage_rate' => 0.8333,
                'points_redeemed_from_import' => 1500, 'points_expired_from_import' => 400,
                'redemption_rate_excluding_import' => 0.833, 'breakage_rate_excluding_import' => 0.1667,
            ],
        ]);

        $loyalty = $this->puntjes()->statistics->get(Period::ThirtyDays)->loyalty;

        self::assertNotNull($loyalty);
        self::assertSame(2000, $loyalty->pointsRedeemed);
        self::assertSame(3.333, $loyalty->redemptionRate);
        self::assertSame(1500, $loyalty->pointsRedeemedFromImport);
        self::assertSame(400, $loyalty->pointsExpiredFromImport);
        self::assertSame(0.833, $loyalty->redemptionRateExcludingImport);
        self::assertSame(0.1667, $loyalty->breakageRateExcludingImport);
    }

    public function test_a_puntjes_that_does_not_send_the_import_fields_yet_reads_as_no_import(): void
    {
        $this->fake->queueData([
            'period' => ['preset' => '30d', 'from' => '', 'to' => '', 'timezone' => 'Europe/Brussels', 'granularity' => 'daily'],
            'commerce' => ['orders' => 0, 'revenue_cents' => 0, 'average_order_value_cents' => 0, 'itemized' => [], 'volume_trend' => []],
            'loyalty' => [
                'points_issued' => 5000, 'points_redeemed' => 1200, 'points_expired' => 300,
                'net_adjustments' => -50, 'redemption_rate' => 0.24, 'breakage_rate' => 0.06,
            ],
        ]);

        $loyalty = $this->puntjes()->statistics->get(Period::ThirtyDays)->loyalty;

        self::assertNotNull($loyalty);
        self::assertSame(0, $loyalty->pointsRedeemedFromImport);
        self::assertSame(0, $loyalty->pointsExpiredFromImport);
        self::assertNull($loyalty->redemptionRateExcludingImport);
        self::assertNull($loyalty->breakageRateExcludingImport);
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

    public function test_a_google_pass_without_a_save_url_throws_instead_of_returning_an_empty_string(): void
    {
        // An empty string here would end up as redirect('') in an integrator's shop.
        $this->fake->queueData(['unexpected' => 'shape']);

        $puntjes = $this->puntjes();

        $this->expectException(TransportException::class);
        $this->expectExceptionMessage('no save_url');

        $puntjes->wallets->googlePassUrl(42);
    }

    public function test_the_escape_hatch_still_authenticates_and_maps_errors(): void
    {
        $this->fake->queueData(['anything' => true]);

        $response = $this->puntjes()->request('GET', '/some/future/endpoint', ['q' => 1]);

        self::assertSame(['anything' => true], $response->dataArray());
        self::assertSame('Bearer test-token', $this->fake->requestAt(1)->getHeaderLine('Authorization'));
        self::assertStringContainsString('q=1', $this->fake->uriAt(1));
    }

    public function test_a_customer_carries_their_card_code_and_consent_provenance(): void
    {
        $this->fake->queueData($this->customerFixture() + [
            'customer_since' => '2024-03-01',
            'loyalty_card_code' => '7KQ4M2XP',
            'marketingConsent' => true,
            'marketingConsentGrantedAt' => '2026-03-03T09:00:00+00:00',
            'marketingConsentGrantedSource' => 'webshop',
            'marketingConsentWithdrawnAt' => null,
            'marketingConsentWithdrawnSource' => null,
        ]);

        $customer = $this->puntjes()->customers->find(42);

        self::assertSame('2024-03-01', $customer->customerSince);
        // Persist this as the QR value rather than digging through identifiers.
        self::assertSame('7KQ4M2XP', $customer->loyaltyCardCode);
        self::assertTrue($customer->hasMarketingConsent());
        self::assertSame('webshop', $customer->marketingConsent->grantedSource);
        self::assertNull($customer->marketingConsent->withdrawnAt);
    }

    public function test_a_customer_payload_without_consent_keys_reads_as_no_consent(): void
    {
        // An older API than this SDK, or a fixture written before the keys existed.
        $this->fake->queueData($this->customerFixture());

        $customer = $this->puntjes()->customers->find(42);

        self::assertFalse($customer->hasMarketingConsent());
        self::assertNull($customer->customerSince);
        self::assertNull($customer->loyaltyCardCode);
    }

    public function test_registering_a_customer_can_record_an_opt_in(): void
    {
        $this->fake->queueData($this->customerFixture(), 201);

        $this->puntjes()->customers->register(new CreateCustomer(
            identifiers: [new CreateIdentifier(IdentifierType::LoyaltyCard, '7KQ4M2XP')],
            customerSince: '2024-03-01',
            marketingConsent: true,
        ));

        $body = $this->fake->bodyAt(1);
        self::assertTrue($body['marketing_consent']);
        self::assertSame('2024-03-01', $body['customer_since']);
    }

    public function test_a_withdrawal_is_sent_as_false_rather_than_omitted(): void
    {
        // false and "not supplied" are different instructions here: one withdraws
        // consent, the other leaves it exactly as it was.
        $this->fake->queueData($this->customerFixture());

        $this->puntjes()->customers->updateByExternalId('PNU-1', new UpdateCustomer(
            marketingConsent: false,
        ));

        $body = $this->fake->bodyAt(1);
        self::assertFalse($body['marketing_consent']);
        self::assertArrayNotHasKey('email', $body);
        self::assertArrayNotHasKey('customer_since', $body);
    }

    public function test_linking_an_external_id_to_a_legacy_customer(): void
    {
        $this->fake->queueData(['external_id' => 'PNU-9'] + $this->customerFixture());

        $customer = $this->puntjes()->customers->linkExternalId('CARD-1', 'PNU-9');

        self::assertSame('PNU-9', $customer->externalId);
        self::assertSame('/api/v1/customers/link-external-id', $this->fake->requestAt(1)->getUri()->getPath());
        self::assertSame(['identifier' => 'CARD-1', 'external_id' => 'PNU-9'], $this->fake->bodyAt(1));
    }

    public function test_relinking_to_a_different_external_id_is_a_conflict(): void
    {
        $this->fake->queueError(409, 'CUSTOMER_ALREADY_LINKED', 'This customer is already linked to external id PNU-1.');

        $this->expectException(ConflictException::class);

        $this->puntjes()->customers->linkExternalId('CARD-1', 'PNU-9');
    }

    public function test_emailing_a_customer_their_loyalty_card(): void
    {
        $this->fake->queueData(['customer_id' => 42, 'channel' => 'email', 'queued' => true], 202);

        $delivery = $this->puntjes()->customers->sendCard(42);

        self::assertTrue($delivery->queued);
        self::assertSame('email', $delivery->channel);
        self::assertSame('/api/v1/customers/42/send-card', $this->fake->requestAt(1)->getUri()->getPath());
        // No channel named: the API picks its default rather than being told one.
        self::assertSame([], $this->fake->bodyAt(1));
    }

    public function test_the_card_can_be_sent_by_your_own_customer_id(): void
    {
        $this->fake->queueData(['customer_id' => 42, 'channel' => 'email', 'queued' => true], 202);

        $this->puntjes()->customers->sendCardByExternalId('PNU-1', channel: 'email');

        self::assertSame(
            '/api/v1/customers/by-external-id/PNU-1/send-card',
            $this->fake->requestAt(1)->getUri()->getPath(),
        );
        self::assertSame(['channel' => 'email'], $this->fake->bodyAt(1));
    }

    public function test_a_card_send_to_a_suppressed_address_is_a_domain_refusal(): void
    {
        // Earlier mail to the address bounced or was marked as spam. The API refuses
        // before it queues anything, so the cooldown is not spent and a till can ask
        // for another address instead of telling the customer the card is on its way.
        $this->fake->queueError(422, 'CUSTOMER_EMAIL_SUPPRESSED', 'Mail to this address bounced.');

        try {
            $this->puntjes()->customers->sendCard(42);
            self::fail('Expected an ApiException.');
        } catch (ApiException $e) {
            self::assertTrue($e->is(ErrorCode::CustomerEmailSuppressed));
            self::assertSame(ErrorCode::CustomerEmailSuppressed, $e->errorCode());
            self::assertSame(422, $e->status());
        }

        self::assertSame(1, $this->fake->apiRequestCount());
    }

    public function test_a_card_send_is_never_retried(): void
    {
        // A replay mails the customer a second time, so a lost response must not be
        // resolved by sending again.
        $this->fake->queueRaw(503, 'upstream unavailable', ['Content-Type' => 'text/html']);

        try {
            $this->puntjes()->customers->sendCard(42);
        } catch (ServerException) {
            // expected
        }

        self::assertSame(1, $this->fake->apiRequestCount());
    }

    public function test_spending_a_discount_voucher(): void
    {
        $this->fake->queueData([
            'voucher_code' => 'BON-ABC12345',
            'discount' => ['kind' => 'fixed', 'amount_cents' => 750],
            'valid_until' => '2026-12-31',
            'consumed_at' => '2026-08-28T10:00:00+00:00',
            'campaign_id' => 4,
            'kind' => 'discount',
            'products' => null,
        ]);

        $result = $this->puntjes()->vouchers->verify('BON-ABC12345', branch: 'centrum');

        self::assertFalse($result->isFreeProduct());
        self::assertSame(750, $result->discount?->amountCents);
        self::assertSame(750, $result->discount?->appliedTo(4200));
        self::assertNull($result->products);
        self::assertSame(['branch' => 'centrum'], $this->fake->bodyAt(1));
        self::assertSame('/api/v1/vouchers/BON-ABC12345/verify', $this->fake->requestAt(1)->getUri()->getPath());
    }

    public function test_a_percentage_voucher_is_applied_to_the_order_total(): void
    {
        $this->fake->queueData([
            'voucher_code' => 'BON-PCT',
            'discount' => ['kind' => 'percentage', 'percentage' => 10],
            'valid_until' => null,
            'consumed_at' => '2026-08-28T10:00:00+00:00',
            'campaign_id' => 4,
            'kind' => 'discount',
            'products' => null,
        ]);

        $result = $this->puntjes()->vouchers->verify('BON-PCT');

        self::assertTrue($result->discount?->isPercentage());
        self::assertSame(420, $result->discount?->appliedTo(4200));
    }

    public function test_spending_a_free_product_voucher_lists_what_to_hand_over(): void
    {
        $this->fake->queueData([
            'voucher_code' => 'BON-GIFT',
            'discount' => null,
            'valid_until' => null,
            'consumed_at' => '2026-08-28T10:00:00+00:00',
            'campaign_id' => 7,
            'kind' => 'free_product',
            'products' => [
                ['id' => 3, 'name' => 'Brood', 'quantity' => 1],
                ['id' => null, 'name' => 'Koffie (verwijderd)', 'quantity' => 2],
            ],
        ]);

        $result = $this->puntjes()->vouchers->verify('BON-GIFT');

        self::assertTrue($result->isFreeProduct());
        self::assertNull($result->discount);

        $products = $result->products;
        self::assertNotNull($products);
        self::assertCount(2, $products);
        self::assertSame('Brood', $products[0]->name);
        self::assertSame(2, $products[1]->quantity);
        // The bon keeps its mint-time snapshot even after the product is deleted.
        self::assertNull($products[1]->id);
    }

    public function test_a_spent_voucher_is_refused_and_never_replayed(): void
    {
        $this->fake->queueError(422, 'VOUCHER_ALREADY_USED', 'This voucher has already been used.');

        try {
            $this->puntjes()->vouchers->verify('BON-ABC12345');
            self::fail('Expected the spent bon to be refused.');
        } catch (ApiException $e) {
            self::assertSame(ErrorCode::VoucherAlreadyUsed, $e->errorCode());
        }

        self::assertSame(1, $this->fake->apiRequestCount());
    }

    public function test_a_verify_without_a_key_sends_no_key_and_is_never_retried(): void
    {
        $this->fake->queueError(503, 'SERVICE_UNAVAILABLE', 'Try again later.');

        try {
            $this->puntjes()->vouchers->verify('BON-ABC12345', branch: 'centrum');
            self::fail('Expected the 503 to surface.');
        } catch (ServerException) {
            // expected
        }

        self::assertSame(['branch' => 'centrum'], $this->fake->bodyAt(1));
        self::assertSame(1, $this->fake->apiRequestCount());
    }

    public function test_a_verify_with_a_key_sends_the_key_and_is_retried(): void
    {
        $this->fake->queueError(503, 'SERVICE_UNAVAILABLE', 'Try again later.');
        $this->fake->queueData($this->spentVoucher());

        $result = $this->puntjes()->vouchers->verify('BON-ABC12345', idempotencyKey: 'sale-77');

        self::assertSame('BON-ABC12345', $result->voucherCode);
        self::assertSame(['idempotency_key' => 'sale-77'], $this->fake->bodyAt(1));
        self::assertSame(['idempotency_key' => 'sale-77'], $this->fake->bodyAt(2));
        self::assertSame(2, $this->fake->apiRequestCount());
    }

    public function test_the_same_key_on_another_voucher_is_a_conflict(): void
    {
        $this->fake->queueError(422, 'IDEMPOTENCY_KEY_CONFLICT', 'This key was used for another voucher.');

        try {
            $this->puntjes()->vouchers->verify('BON-OTHER', idempotencyKey: 'sale-77');
            self::fail('Expected the key conflict to surface.');
        } catch (ApiException $e) {
            self::assertSame(ErrorCode::IdempotencyKeyConflict, $e->errorCode());
        }

        self::assertSame(1, $this->fake->apiRequestCount());
    }

    public function test_finding_a_valid_voucher_reads_it_without_spending_it(): void
    {
        $this->fake->queueData($this->lookedUpVoucher('valid', consumedAt: null));

        $voucher = $this->puntjes()->vouchers->find('BON-ABC12345');

        self::assertSame('GET', $this->fake->requestAt(1)->getMethod());
        self::assertSame('/api/v1/vouchers/BON-ABC12345', $this->fake->requestAt(1)->getUri()->getPath());
        self::assertSame(VoucherStatus::Valid, $voucher->status);
        self::assertTrue($voucher->status?->isRedeemable());
        self::assertNull($voucher->consumedAt);
        self::assertSame('BON-ABC12345', $voucher->voucherCode);
        self::assertSame(750, $voucher->discount?->amountCents);
        self::assertSame('2026-12-31', $voucher->validUntil);
        self::assertSame(4, $voucher->campaignId);
        self::assertSame('discount', $voucher->kind);
        self::assertFalse($voucher->isFreeProduct());
    }

    public function test_finding_a_used_voucher_reports_when_it_was_spent(): void
    {
        $this->fake->queueData($this->lookedUpVoucher('used', consumedAt: '2026-10-01T09:30:00+00:00'));

        $voucher = $this->puntjes()->vouchers->find('BON-ABC12345');

        self::assertSame(VoucherStatus::Used, $voucher->status);
        self::assertFalse($voucher->status?->isRedeemable());
        self::assertSame('2026-10-01T09:30:00+00:00', $voucher->consumedAt);
    }

    public function test_finding_an_expired_voucher_answers_instead_of_throwing(): void
    {
        $this->fake->queueData($this->lookedUpVoucher('expired', consumedAt: null));

        $voucher = $this->puntjes()->vouchers->find('BON-ABC12345');

        self::assertSame(VoucherStatus::Expired, $voucher->status);
        self::assertFalse($voucher->status?->isRedeemable());
    }

    public function test_an_unknown_voucher_status_reads_as_null(): void
    {
        $this->fake->queueData($this->lookedUpVoucher('frozen', consumedAt: null));

        $voucher = $this->puntjes()->vouchers->find('BON-ABC12345');

        self::assertNull($voucher->status);
        self::assertSame('BON-ABC12345', $voucher->voucherCode);
    }

    public function test_finding_an_unknown_voucher_throws_not_found(): void
    {
        $this->fake->queueError(404, 'VOUCHER_NOT_FOUND', 'No voucher found for this code.');

        try {
            $this->puntjes()->vouchers->find('BON-NOPE');
            self::fail('Expected the unknown code to be refused.');
        } catch (NotFoundException $e) {
            self::assertSame(ErrorCode::VoucherNotFound, $e->errorCode());
        }
    }

    public function test_a_voucher_product_carries_its_item_number_when_sent(): void
    {
        $this->fake->queueData($this->lookedUpVoucher('valid', consumedAt: null, kind: 'free_product'));
        $this->fake->queueData($this->spentVoucher(kind: 'free_product'));

        $found = $this->puntjes()->vouchers->find('BON-GIFT');
        $spent = $this->puntjes()->vouchers->verify('BON-GIFT');

        self::assertTrue($found->isFreeProduct());
        self::assertNull($found->discount);
        self::assertNotNull($found->products);
        self::assertNotNull($spent->products);
        self::assertSame('SKU-BROOD', $found->products[0]->productReference);
        self::assertSame('Koffie (verwijderd)', $found->products[1]->name);
        self::assertNull($found->products[1]->productReference);
        self::assertSame('SKU-BROOD', $spent->products[0]->productReference);
    }

    /** @return array<string, mixed> */
    private function spentVoucher(string $kind = 'discount'): array
    {
        return [
            'voucher_code' => 'BON-ABC12345',
            'consumed_at' => '2026-10-06T10:00:00+00:00',
        ] + $this->voucherBody($kind);
    }

    /** @return array<string, mixed> */
    private function lookedUpVoucher(string $status, ?string $consumedAt, string $kind = 'discount'): array
    {
        return [
            'voucher_code' => 'BON-ABC12345',
            'consumed_at' => $consumedAt,
            'status' => $status,
        ] + $this->voucherBody($kind);
    }

    /** @return array<string, mixed> */
    private function voucherBody(string $kind): array
    {
        $freeProduct = $kind === 'free_product';

        return [
            'discount' => $freeProduct ? null : ['kind' => 'fixed', 'amount_cents' => 750, 'product_reference' => null],
            'valid_until' => '2026-12-31',
            'campaign_id' => 4,
            'kind' => $kind,
            'products' => $freeProduct ? [
                ['id' => 3, 'name' => 'Brood', 'quantity' => 1, 'product_reference' => 'SKU-BROOD'],
                ['id' => null, 'name' => 'Koffie (verwijderd)', 'quantity' => 1, 'product_reference' => null],
            ] : null,
        ];
    }
}
