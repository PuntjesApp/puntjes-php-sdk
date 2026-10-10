<?php

declare(strict_types=1);

namespace Puntjes\Spike\Tests;

use PHPUnit\Framework\TestCase;
use Puntjes\Spike\Oag\Api;
use Puntjes\Spike\Oag\ApiException;
use Puntjes\Spike\Oag\Configuration;
use Puntjes\Spike\Oag\Model;
use Puntjes\Spike\Tests\Support\Live;

/**
 * Calls all 34 operations through the OpenAPI Generator 7.26 `php` client with `library=psr-18`.
 *
 * The tests run in declaration order and share the ids they create. Each test records its
 * operation only after its assertions pass; the last test fails on every operation not recorded.
 */
final class OagContractTest extends TestCase
{
    private const CLIENT = 'oag';

    /** @var array<string, int|string> */
    private static array $state = [];

    private static function config(?string $token = null): Configuration
    {
        return (new Configuration)->setHost(Live::baseUrl())->setAccessToken($token ?? Live::token());
    }

    /**
     * @template T of object
     *
     * @param  class-string<T>  $api
     * @return T
     */
    private static function api(string $api, ?string $token = null): object
    {
        return new $api(null, self::config($token));
    }

    private static function sku(string $suffix): string
    {
        return Live::runId().'-'.$suffix;
    }

    private static function email(string $suffix): string
    {
        return Live::runId().'-'.$suffix.'@example.test';
    }

    public function test_check_health(): void
    {
        $result = self::api(Api\HealthApi::class)->checkHealth();

        self::assertInstanceOf(Model\CheckHealth200Response::class, $result);
        Live::hit(self::CLIENT, 'checkHealth');
    }

    public function test_show_me(): void
    {
        $result = self::api(Api\MeApi::class)->showMe();

        self::assertInstanceOf(Model\ShowMe200Response::class, $result);
        self::assertNotSame('', $result->getData()->getDisplayName());
        Live::hit(self::CLIENT, 'showMe');
    }

    public function test_a_token_the_api_cannot_read_is_an_error_not_an_answer(): void
    {
        try {
            $result = self::api(Api\MeApi::class, 'not-a-token')->showMe();
        } catch (ApiException $e) {
            self::assertSame(401, $e->getCode());

            return;
        }

        self::assertNotInstanceOf(Model\ErrorResponse::class, $result, 'A 401 came back as a return value, not an exception');
    }

    public function test_show_statistics(): void
    {
        $result = self::api(Api\StatisticsApi::class)->showStatistics('30d');

        self::assertInstanceOf(Model\ShowStatistics200Response::class, $result);
        Live::hit(self::CLIENT, 'showStatistics');
    }

    public function test_list_campaigns_types_the_config_union(): void
    {
        $result = self::api(Api\CampaignsApi::class)->listCampaigns();

        self::assertInstanceOf(Model\ListCampaigns200Response::class, $result);
        Live::hit(self::CLIENT, 'listCampaigns');

        $moments = array_filter($result->getData()->getData(), fn (Model\CampaignListData $c) => $c->getFamily() === 'customer_moment');
        self::assertNotEmpty($moments, 'The voucher fixture creates a customer-moment campaign');
        self::assertInstanceOf(Model\MomentCampaignConfigData::class, array_values($moments)[0]->getConfig());
    }

    public function test_create_product(): void
    {
        $result = self::api(Api\ProductsApi::class)->createProduct(new Model\CreateProductData([
            'external_id' => self::sku('a'),
            'name' => 'Spike product A',
            'price_cents' => 250,
            'category' => Live::runId(),
        ]));

        self::assertInstanceOf(Model\CreateProduct201Response::class, $result);
        self::assertSame(self::sku('a'), $result->getData()->getExternalId());
        Live::hit(self::CLIENT, 'createProduct');
    }

    public function test_a_validation_error_is_an_error_not_an_answer(): void
    {
        try {
            $result = self::api(Api\ProductsApi::class)->createProduct(new Model\CreateProductData([
                'external_id' => self::sku('invalid'),
                'name' => '',
            ]));
        } catch (ApiException $e) {
            self::assertSame(422, $e->getCode());

            return;
        }

        self::assertNotInstanceOf(Model\ErrorResponse::class, $result, 'A 422 came back as a return value, not an exception');
    }

    public function test_show_product(): void
    {
        $result = self::api(Api\ProductsApi::class)->showProduct(self::sku('a'));

        self::assertSame('Spike product A', $result->getData()->getName());
        Live::hit(self::CLIENT, 'showProduct');
    }

    public function test_update_product(): void
    {
        $result = self::api(Api\ProductsApi::class)->updateProduct(self::sku('a'), new Model\UpdateProductData(['name' => 'Spike product A2']));

        self::assertSame('Spike product A2', $result->getData()->getName());
        Live::hit(self::CLIENT, 'updateProduct');
    }

    public function test_upsert_product(): void
    {
        $result = self::api(Api\ProductsApi::class)->upsertProduct(self::sku('b'), new Model\UpsertProductData(['name' => 'Spike product B', 'category' => Live::runId()]));

        self::assertSame(self::sku('b'), $result->getData()->getExternalId());
        Live::hit(self::CLIENT, 'upsertProduct');
    }

    public function test_list_products(): void
    {
        $result = self::api(Api\ProductsApi::class)->listProducts(null, Live::runId());

        self::assertCount(2, $result->getData()->getData());
        Live::hit(self::CLIENT, 'listProducts');
    }

    public function test_bulk_upsert_products(): void
    {
        $result = self::api(Api\ProductsApi::class)->bulkUpsertProducts();

        self::assertInstanceOf(Model\BulkUpsertProducts200Response::class, $result, 'The spec gives this operation no request body, so the client cannot send the products');
        Live::hit(self::CLIENT, 'bulkUpsertProducts');
    }

    public function test_create_reward_from_product(): void
    {
        $result = self::api(Api\ProductsApi::class)->createRewardFromProduct(self::sku('a'), new Model\CreateRewardFromProductData([
            'point_cost' => 1,
            'idempotency_key' => Live::runId().'-reward',
        ]));

        self::assertInstanceOf(Model\CreateRewardFromProduct201Response::class, $result);
        self::$state['reward'] = $result->getData()->getId();
        Live::hit(self::CLIENT, 'createRewardFromProduct');
    }

    public function test_list_rewards(): void
    {
        $result = self::api(Api\RewardsApi::class)->listRewards();

        $ids = array_map(fn ($r) => $r->getId(), $result->getData());
        self::assertContains(self::$state['reward'], $ids);
        Live::hit(self::CLIENT, 'listRewards');
    }

    public function test_register_customer(): void
    {
        $result = self::api(Api\CustomersApi::class)->registerCustomer(new Model\CreateCustomerData([
            'email' => self::email('a'),
            'external_id' => self::sku('cust-a'),
            'first_name' => 'Spike',
            'phone' => null,
        ]));

        self::assertInstanceOf(Model\RegisterCustomer201Response::class, $result);
        self::$state['customer'] = $result->getData()->getId();
        Live::hit(self::CLIENT, 'registerCustomer');
    }

    public function test_show_customer_reads_the_nullable_fields(): void
    {
        $customer = self::api(Api\CustomersApi::class)->showCustomer((string) self::$state['customer'])->getData();

        self::assertInstanceOf(Model\CustomerDetailData::class, $customer);
        self::assertSame(self::email('a'), $customer->getEmail());
        self::assertNull($customer->getPhone());
        self::assertNull($customer->getDeactivatedAt());
        Live::hit(self::CLIENT, 'showCustomer');
    }

    public function test_an_unknown_customer_is_a_typed_not_found(): void
    {
        try {
            $result = self::api(Api\CustomersApi::class)->showCustomer('999999999');
        } catch (ApiException $e) {
            self::assertSame(404, $e->getCode());

            return;
        }

        self::assertNotInstanceOf(Model\ErrorResponse::class, $result, 'A 404 came back as a return value, not an exception');
    }

    public function test_lookup_customer(): void
    {
        $result = self::api(Api\CustomersApi::class)->lookupCustomer(null, self::sku('cust-a'));

        self::assertSame(self::$state['customer'], $result->getData()->getId());
        Live::hit(self::CLIENT, 'lookupCustomer');
    }

    public function test_update_customer_by_external_id(): void
    {
        $result = self::api(Api\CustomersApi::class)->updateCustomerByExternalId(self::sku('cust-a'), new Model\UpdateCustomerData(['first_name' => 'Spiked']));

        self::assertSame('Spiked', $result->getData()->getFirstName());
        Live::hit(self::CLIENT, 'updateCustomerByExternalId');
    }

    public function test_replace_customer_by_external_id(): void
    {
        $result = self::api(Api\CustomersApi::class)->replaceCustomerByExternalId(self::sku('cust-a'), new Model\UpdateCustomerData([
            'email' => self::email('a'),
            'first_name' => 'Replaced',
        ]));

        self::assertSame('Replaced', $result->getData()->getFirstName());
        Live::hit(self::CLIENT, 'replaceCustomerByExternalId');
    }

    public function test_link_customer_external_id(): void
    {
        self::api(Api\CustomersApi::class)->registerCustomer(new Model\CreateCustomerData(['email' => self::email('b')]));

        $result = self::api(Api\CustomersApi::class)->linkCustomerExternalId(new Model\LinkExternalIdData([
            'identifier' => self::email('b'),
            'external_id' => self::sku('cust-b'),
        ]));

        self::assertSame(self::sku('cust-b'), $result->getData()->getExternalId());
        Live::hit(self::CLIENT, 'linkCustomerExternalId');
    }

    public function test_submit_transaction(): void
    {
        $result = self::api(Api\TransactionsApi::class)->submitTransaction(new Model\CreateTransactionData([
            'identifier' => self::email('a'),
            'total_amount' => 1000,
            'idempotency_key' => Live::runId().'-tx',
        ]));

        self::assertInstanceOf(Model\SubmitTransaction201Response::class, $result);
        Live::hit(self::CLIENT, 'submitTransaction');
    }

    public function test_list_customer_transactions(): void
    {
        $result = self::api(Api\TransactionsApi::class)->listCustomerTransactions((string) self::$state['customer']);

        self::assertCount(1, $result->getData()->getData());
        Live::hit(self::CLIENT, 'listCustomerTransactions');
    }

    public function test_adjust_wallet(): void
    {
        $result = self::api(Api\WalletApi::class)->adjustWallet((string) self::$state['customer'], new Model\AdjustWalletApiData([
            'amount' => 50,
            'reason' => 'spike',
            'idempotency_key' => Live::runId().'-adjust',
        ]));

        self::assertInstanceOf(Model\AdjustWallet200Response::class, $result);
        Live::hit(self::CLIENT, 'adjustWallet');
    }

    public function test_show_wallet(): void
    {
        $result = self::api(Api\WalletApi::class)->showWallet((string) self::$state['customer']);

        self::assertGreaterThanOrEqual(50, $result->getData()->getBalance());
        Live::hit(self::CLIENT, 'showWallet');
    }

    public function test_list_ledger_entries(): void
    {
        $result = self::api(Api\WalletApi::class)->listLedgerEntries((string) self::$state['customer']);

        self::assertNotEmpty($result->getData()->getData());
        Live::hit(self::CLIENT, 'listLedgerEntries');
    }

    public function test_redeem_reward(): void
    {
        $result = self::api(Api\RedemptionsApi::class)->redeemReward(new Model\CreateRedemptionData([
            'identifier' => self::email('a'),
            'reward_id' => self::$state['reward'],
            'idempotency_key' => Live::runId().'-redeem',
        ]));

        self::assertInstanceOf(Model\RedeemReward201Response::class, $result);
        self::$state['redemption'] = $result->getData()->getConfirmationCode();
        Live::hit(self::CLIENT, 'redeemReward');
    }

    public function test_list_customer_redemptions(): void
    {
        $result = self::api(Api\RedemptionsApi::class)->listCustomerRedemptions((string) self::$state['customer']);

        self::assertCount(1, $result->getData()->getData());
        Live::hit(self::CLIENT, 'listCustomerRedemptions');
    }

    public function test_lookup_redemption(): void
    {
        $result = self::api(Api\RedemptionsApi::class)->lookupRedemption((string) self::$state['redemption']);

        self::assertInstanceOf(Model\LookupRedemption200Response::class, $result);
        Live::hit(self::CLIENT, 'lookupRedemption');
    }

    public function test_verify_redemption(): void
    {
        $result = self::api(Api\RedemptionsApi::class)->verifyRedemption((string) self::$state['redemption']);

        self::assertInstanceOf(Model\VerifyRedemption200Response::class, $result);
        Live::hit(self::CLIENT, 'verifyRedemption');
    }

    public function test_list_customer_vouchers(): void
    {
        $result = self::api(Api\VouchersApi::class)->listCustomerVouchers((string) Live::voucher()['customer']);

        $codes = array_map(fn ($v) => $v->getCode(), $result->getData());
        self::assertContains(Live::voucher()['code'], $codes);
        Live::hit(self::CLIENT, 'listCustomerVouchers');
    }

    public function test_lookup_voucher_types_the_discount_union(): void
    {
        $result = self::api(Api\VouchersApi::class)->lookupVoucher(Live::voucher()['code']);

        self::assertInstanceOf(Model\LookupVoucher200Response::class, $result);
        Live::hit(self::CLIENT, 'lookupVoucher');
        self::assertInstanceOf(Model\VoucherPercentageDiscountData::class, $result->getData()->getDiscount());
    }

    public function test_verify_voucher(): void
    {
        $result = self::api(Api\VouchersApi::class)->verifyVoucher(Live::voucher()['code'], new Model\VerifyCampaignVoucherData([
            'idempotency_key' => Live::runId().'-voucher',
        ]));

        self::assertInstanceOf(Model\VerifyVoucher200Response::class, $result);
        Live::hit(self::CLIENT, 'verifyVoucher');
    }

    public function test_download_wallet_pass(): void
    {
        $result = self::api(Api\WalletApi::class)->downloadWalletPass((string) self::$state['customer'], 'apple');

        self::assertNotNull($result, 'Apple answers a pkpass file the spec does not describe');
        Live::hit(self::CLIENT, 'downloadWalletPass');
    }

    public function test_send_loyalty_card_refusal_path(): void
    {
        $result = null;

        try {
            $result = self::api(Api\CustomersApi::class)->sendLoyaltyCard('999999999', new Model\SendLoyaltyCardRequestData(['channel' => 'email']));
        } catch (ApiException $e) {
            self::assertSame(404, $e->getCode());
        }

        if ($result !== null) {
            self::assertInstanceOf(Model\ErrorResponse::class, $result);
            self::assertSame('CUSTOMER_NOT_FOUND', $result->getError()->getCode());
        }

        Live::hit(self::CLIENT, 'sendLoyaltyCard');
    }

    public function test_send_loyalty_card_by_external_id_refusal_path(): void
    {
        $result = null;

        try {
            $result = self::api(Api\CustomersApi::class)->sendLoyaltyCardByExternalId(self::sku('nobody'), new Model\SendLoyaltyCardRequestData(['channel' => 'email']));
        } catch (ApiException $e) {
            self::assertSame(404, $e->getCode());
        }

        if ($result !== null) {
            self::assertInstanceOf(Model\ErrorResponse::class, $result);
            self::assertSame('CUSTOMER_NOT_FOUND', $result->getError()->getCode());
        }

        Live::hit(self::CLIENT, 'sendLoyaltyCardByExternalId');
    }

    public function test_delete_product(): void
    {
        $api = self::api(Api\ProductsApi::class);
        $api->deleteProduct(self::sku('a'));
        $api->deleteProduct(self::sku('b'));

        self::assertCount(0, $api->listProducts(null, Live::runId())->getData()->getData());
        Live::hit(self::CLIENT, 'deleteProduct');
    }

    public function test_every_operation_was_called(): void
    {
        self::assertSame([], Live::missed(self::CLIENT));
    }
}
