<?php

declare(strict_types=1);

namespace Puntjes\Spike\Tests;

use Http\Promise\FulfilledPromise;
use Http\Promise\Promise;
use Microsoft\Kiota\Abstractions\ApiException;
use Microsoft\Kiota\Abstractions\Authentication\AccessTokenProvider;
use Microsoft\Kiota\Abstractions\Authentication\AllowedHostsValidator;
use Microsoft\Kiota\Abstractions\Authentication\BaseBearerTokenAuthenticationProvider;
use Microsoft\Kiota\Http\GuzzleRequestAdapter;
use PHPUnit\Framework\TestCase;
use Puntjes\Spike\Kiota\Customers\Item\WalletPass\GetPlatformQueryParameterType;
use Puntjes\Spike\Kiota\Customers\Item\WalletPass\WalletPassRequestBuilderGetRequestConfiguration;
use Puntjes\Spike\Kiota\Customers\Lookup\LookupRequestBuilderGetRequestConfiguration;
use Puntjes\Spike\Kiota\Models;
use Puntjes\Spike\Kiota\Products\ProductsRequestBuilderGetRequestConfiguration;
use Puntjes\Spike\Kiota\PuntjesClient;
use Puntjes\Spike\Tests\Support\Live;

/**
 * Calls all 34 operations through the Kiota 1.35 PHP client, the same scenario as OagContractTest.
 *
 * Kiota's only shipped request adapter is GuzzleRequestAdapter, so this suite runs on Guzzle.
 */
final class KiotaContractTest extends TestCase
{
    private const CLIENT = 'kiota';

    /** @var array<string, int|string> */
    private static array $state = [];

    private static function client(?string $token = null): PuntjesClient
    {
        $tokens = new class($token ?? Live::token()) implements AccessTokenProvider
        {
            public function __construct(private readonly string $token) {}

            public function getAuthorizationTokenAsync(string $url, array $additionalAuthenticationContext = []): Promise
            {
                return new FulfilledPromise($this->token);
            }

            public function getAllowedHostsValidator(): AllowedHostsValidator
            {
                return new AllowedHostsValidator;
            }
        };

        $adapter = new GuzzleRequestAdapter(new BaseBearerTokenAuthenticationProvider($tokens));
        $adapter->setBaseUrl(Live::baseUrl());

        return new PuntjesClient($adapter);
    }

    private static function sku(string $suffix): string
    {
        return Live::runId().'-'.$suffix;
    }

    private static function email(string $suffix): string
    {
        return Live::runId().'-'.$suffix.'@example.test';
    }

    private static function customer(): string
    {
        return (string) self::$state['customer'];
    }

    public function test_check_health(): void
    {
        self::assertNotNull(self::client()->health()->get()->wait());
        Live::hit(self::CLIENT, 'checkHealth');
    }

    public function test_show_me(): void
    {
        self::assertNotSame('', self::client()->me()->get()->wait()->getData()->getDisplayName());
        Live::hit(self::CLIENT, 'showMe');
    }

    public function test_a_token_the_api_cannot_read_is_a_typed_error(): void
    {
        try {
            self::client('not-a-token')->me()->get()->wait();
            self::fail('No exception for a 401');
        } catch (Models\ErrorResponse $e) {
            self::assertSame(401, $e->getResponseStatusCode());
            self::assertSame('UNAUTHENTICATED', $e->getError()->getCode());
        }
    }

    public function test_show_statistics(): void
    {
        self::assertNotNull(self::client()->statistics()->get()->wait()->getData());
        Live::hit(self::CLIENT, 'showStatistics');
    }

    public function test_list_campaigns_types_the_config_union(): void
    {
        $campaigns = self::client()->campaigns()->get()->wait()->getData()->getData();
        Live::hit(self::CLIENT, 'listCampaigns');

        $moments = array_values(array_filter($campaigns, fn (Models\CampaignListData $c) => $c->getFamily() === 'customer_moment'));
        self::assertNotEmpty($moments, 'The voucher fixture creates a customer-moment campaign');
        self::assertInstanceOf(Models\MomentCampaignConfigData::class, $moments[0]->getConfig()->getMomentCampaignConfigData());
    }

    public function test_create_product(): void
    {
        $body = new Models\CreateProductData;
        $body->setExternalId(self::sku('a'));
        $body->setName('Spike product A');
        $body->setPriceCents(250);
        $body->setCategory(Live::runId());

        self::assertSame(self::sku('a'), self::client()->products()->post($body)->wait()->getData()->getExternalId());
        Live::hit(self::CLIENT, 'createProduct');
    }

    public function test_a_validation_error_is_a_typed_error(): void
    {
        $body = new Models\CreateProductData;
        $body->setExternalId(self::sku('invalid'));
        $body->setName('');

        try {
            self::client()->products()->post($body)->wait();
            self::fail('No exception for a 422');
        } catch (Models\ErrorResponse $e) {
            self::assertSame(422, $e->getResponseStatusCode());
            self::assertSame('VALIDATION_ERROR', $e->getError()->getCode());
        }
    }

    public function test_show_product(): void
    {
        self::assertSame('Spike product A', self::client()->products()->byExternalId(self::sku('a'))->get()->wait()->getData()->getName());
        Live::hit(self::CLIENT, 'showProduct');
    }

    public function test_update_product(): void
    {
        $body = new Models\UpdateProductData;
        $body->setName('Spike product A2');

        self::assertSame('Spike product A2', self::client()->products()->byExternalId(self::sku('a'))->patch($body)->wait()->getData()->getName());
        Live::hit(self::CLIENT, 'updateProduct');
    }

    public function test_upsert_product(): void
    {
        $body = new Models\UpsertProductData;
        $body->setName('Spike product B');
        $body->setCategory(Live::runId());

        self::assertSame(self::sku('b'), self::client()->products()->byExternalId(self::sku('b'))->put($body)->wait()->getData()->getExternalId());
        Live::hit(self::CLIENT, 'upsertProduct');
    }

    public function test_list_products(): void
    {
        $config = new ProductsRequestBuilderGetRequestConfiguration(queryParameters: ProductsRequestBuilderGetRequestConfiguration::createQueryParameters(category: Live::runId()));

        self::assertCount(2, self::client()->products()->get($config)->wait()->getData()->getData());
        Live::hit(self::CLIENT, 'listProducts');
    }

    public function test_bulk_upsert_products(): void
    {
        $item = new Models\BulkUpsertItemData;
        $item->setExternalId(self::sku('c'));
        $item->setName('Spike product C');
        $item->setCategory(Live::runId());
        $body = new \Puntjes\Spike\Kiota\Products\Batch\BatchPostRequestBody;
        $body->setProducts([$item]);

        self::assertSame(1, self::client()->products()->batch()->post($body)->wait()->getData()->getSummary()->getTotal());
        Live::hit(self::CLIENT, 'bulkUpsertProducts');
    }

    public function test_create_reward_from_product(): void
    {
        $body = new Models\CreateRewardFromProductData;
        $body->setPointCost(1);
        $body->setIdempotencyKey(Live::runId().'-reward');

        self::$state['reward'] = self::client()->products()->byExternalId(self::sku('a'))->reward()->post($body)->wait()->getData()->getId();
        self::assertIsInt(self::$state['reward']);
        Live::hit(self::CLIENT, 'createRewardFromProduct');
    }

    public function test_list_rewards(): void
    {
        $ids = array_map(fn ($r) => $r->getId(), self::client()->rewards()->get()->wait()->getData());

        self::assertContains(self::$state['reward'], $ids);
        Live::hit(self::CLIENT, 'listRewards');
    }

    public function test_register_customer(): void
    {
        $body = new Models\CreateCustomerData;
        $body->setEmail(self::email('a'));
        $body->setExternalId(self::sku('cust-a'));
        $body->setFirstName('Spike');
        $body->setPhone(null);

        self::$state['customer'] = self::client()->customers()->post($body)->wait()->getData()->getId();
        self::assertIsInt(self::$state['customer']);
        Live::hit(self::CLIENT, 'registerCustomer');
    }

    public function test_show_customer_reads_the_nullable_fields(): void
    {
        $customer = self::client()->customers()->byCustomer(self::customer())->get()->wait()->getData();

        self::assertSame(self::email('a'), $customer->getEmail());
        self::assertNull($customer->getPhone());
        self::assertNull($customer->getDeactivatedAt());
        Live::hit(self::CLIENT, 'showCustomer');
    }

    public function test_an_unknown_customer_is_a_typed_not_found(): void
    {
        try {
            self::client()->customers()->byCustomer('999999999')->get()->wait();
            self::fail('No exception for a 404');
        } catch (Models\ErrorResponse $e) {
            self::assertSame('CUSTOMER_NOT_FOUND', $e->getError()->getCode());
        }
    }

    public function test_lookup_customer(): void
    {
        $config = new LookupRequestBuilderGetRequestConfiguration(queryParameters: LookupRequestBuilderGetRequestConfiguration::createQueryParameters(external_id: self::sku('cust-a')));

        self::assertSame(self::$state['customer'], self::client()->customers()->lookup()->get($config)->wait()->getData()->getId());
        Live::hit(self::CLIENT, 'lookupCustomer');
    }

    public function test_update_customer_by_external_id(): void
    {
        $body = new Models\UpdateCustomerData;
        $body->setFirstName('Spiked');

        self::assertSame('Spiked', self::client()->customers()->byExternalId()->byExternalId(self::sku('cust-a'))->patch($body)->wait()->getData()->getFirstName());
        Live::hit(self::CLIENT, 'updateCustomerByExternalId');
    }

    public function test_replace_customer_by_external_id(): void
    {
        $body = new Models\UpdateCustomerData;
        $body->setEmail(self::email('a'));
        $body->setFirstName('Replaced');

        self::assertSame('Replaced', self::client()->customers()->byExternalId()->byExternalId(self::sku('cust-a'))->put($body)->wait()->getData()->getFirstName());
        Live::hit(self::CLIENT, 'replaceCustomerByExternalId');
    }

    public function test_link_customer_external_id(): void
    {
        $customer = new Models\CreateCustomerData;
        $customer->setEmail(self::email('b'));
        self::client()->customers()->post($customer)->wait();

        $body = new Models\LinkExternalIdData;
        $body->setIdentifier(self::email('b'));
        $body->setExternalId(self::sku('cust-b'));

        self::assertSame(self::sku('cust-b'), self::client()->customers()->linkExternalId()->post($body)->wait()->getData()->getExternalId());
        Live::hit(self::CLIENT, 'linkCustomerExternalId');
    }

    public function test_submit_transaction(): void
    {
        $body = new Models\CreateTransactionData;
        $body->setIdentifier(self::email('a'));
        $body->setTotalAmount(1000);
        $body->setIdempotencyKey(Live::runId().'-tx');

        self::assertNotNull(self::client()->transactions()->post($body)->wait()->getData());
        Live::hit(self::CLIENT, 'submitTransaction');
    }

    public function test_list_customer_transactions(): void
    {
        self::assertCount(1, self::client()->customers()->byCustomer(self::customer())->transactions()->get()->wait()->getData()->getData());
        Live::hit(self::CLIENT, 'listCustomerTransactions');
    }

    public function test_adjust_wallet(): void
    {
        $body = new Models\AdjustWalletApiData;
        $body->setAmount(50);
        $body->setReason('spike');
        $body->setIdempotencyKey(Live::runId().'-adjust');

        self::assertNotNull(self::client()->customers()->byCustomer(self::customer())->wallet()->adjust()->post($body)->wait()->getData());
        Live::hit(self::CLIENT, 'adjustWallet');
    }

    public function test_show_wallet(): void
    {
        self::assertGreaterThanOrEqual(50, self::client()->customers()->byCustomer(self::customer())->wallet()->get()->wait()->getData()->getBalance());
        Live::hit(self::CLIENT, 'showWallet');
    }

    public function test_list_ledger_entries(): void
    {
        self::assertNotEmpty(self::client()->customers()->byCustomer(self::customer())->ledger()->get()->wait()->getData()->getData());
        Live::hit(self::CLIENT, 'listLedgerEntries');
    }

    public function test_redeem_reward(): void
    {
        $body = new Models\CreateRedemptionData;
        $body->setIdentifier(self::email('a'));
        $body->setRewardId((int) self::$state['reward']);
        $body->setIdempotencyKey(Live::runId().'-redeem');

        self::$state['redemption'] = self::client()->redemptions()->post($body)->wait()->getData()->getConfirmationCode();
        self::assertIsString(self::$state['redemption']);
        Live::hit(self::CLIENT, 'redeemReward');
    }

    public function test_list_customer_redemptions(): void
    {
        self::assertCount(1, self::client()->customers()->byCustomer(self::customer())->redemptions()->get()->wait()->getData()->getData());
        Live::hit(self::CLIENT, 'listCustomerRedemptions');
    }

    public function test_lookup_redemption(): void
    {
        self::assertNotNull(self::client()->redemptions()->byCode((string) self::$state['redemption'])->get()->wait()->getData());
        Live::hit(self::CLIENT, 'lookupRedemption');
    }

    public function test_verify_redemption(): void
    {
        self::assertNotNull(self::client()->redemptions()->byCode((string) self::$state['redemption'])->verify()->post()->wait()->getData());
        Live::hit(self::CLIENT, 'verifyRedemption');
    }

    public function test_list_customer_vouchers(): void
    {
        $vouchers = self::client()->customers()->byCustomer((string) Live::voucher()['customer'])->vouchers()->get()->wait()->getData();

        self::assertContains(Live::voucher()['code'], array_map(fn ($v) => $v->getVoucherCode(), $vouchers));
        Live::hit(self::CLIENT, 'listCustomerVouchers');
    }

    public function test_lookup_voucher_types_the_discount_union(): void
    {
        $voucher = self::client()->vouchers()->byCode(Live::voucher()['code'])->get()->wait()->getData();
        Live::hit(self::CLIENT, 'lookupVoucher');

        self::assertInstanceOf(Models\VoucherPercentageDiscountData::class, $voucher->getDiscount()->getVoucherPercentageDiscountData());
    }

    public function test_verify_voucher(): void
    {
        $body = new Models\VerifyCampaignVoucherData;
        $body->setIdempotencyKey(Live::runId().'-voucher');

        self::assertNotNull(self::client()->vouchers()->byCode(Live::voucher()['code'])->verify()->post($body)->wait()->getData());
        Live::hit(self::CLIENT, 'verifyVoucher');
    }

    public function test_download_wallet_pass_needs_a_hand_written_send(): void
    {
        $config = new WalletPassRequestBuilderGetRequestConfiguration(queryParameters: WalletPassRequestBuilderGetRequestConfiguration::createQueryParameters(new GetPlatformQueryParameterType('apple')));
        $client = self::client();
        $request = $client->customers()->byCustomer(self::customer())->walletPass()->toGetRequestInformation($config);
        $request->removeHeader('Accept');
        $request->addHeader('Accept', 'application/vnd.apple.pkpass');

        $adapter = (new \ReflectionProperty($client, 'requestAdapter'))->getValue($client);
        $stream = $adapter->sendPrimitiveAsync($request, \Psr\Http\Message\StreamInterface::class, ['XXX' => [Models\ErrorResponse::class, 'createFromDiscriminatorValue']])->wait();

        self::assertStringStartsWith('PK', (string) $stream);
        Live::hit(self::CLIENT, 'downloadWalletPass');
    }

    public function test_send_loyalty_card_refusal_path(): void
    {
        $body = new Models\SendLoyaltyCardRequestData;
        $body->setChannel(new Models\SendLoyaltyCardRequestData_channel('email'));

        try {
            self::client()->customers()->byCustomer('999999999')->sendCard()->post($body)->wait();
            self::fail('No exception for a 404');
        } catch (Models\ErrorResponse $e) {
            self::assertSame('CUSTOMER_NOT_FOUND', $e->getError()->getCode());
        }

        Live::hit(self::CLIENT, 'sendLoyaltyCard');
    }

    public function test_send_loyalty_card_by_external_id_refusal_path(): void
    {
        $body = new Models\SendLoyaltyCardRequestData;

        try {
            self::client()->customers()->byExternalId()->byExternalId(self::sku('nobody'))->sendCard()->post($body)->wait();
            self::fail('No exception for a 404');
        } catch (Models\ErrorResponse $e) {
            self::assertSame('EXTERNAL_ID_NOT_FOUND', $e->getError()->getCode());
        }

        Live::hit(self::CLIENT, 'sendLoyaltyCardByExternalId');
    }

    public function test_delete_product(): void
    {
        $products = self::client()->products();
        $products->byExternalId(self::sku('a'))->delete()->wait();
        $products->byExternalId(self::sku('b'))->delete()->wait();
        $products->byExternalId(self::sku('c'))->delete()->wait();

        $config = new ProductsRequestBuilderGetRequestConfiguration(queryParameters: ProductsRequestBuilderGetRequestConfiguration::createQueryParameters(category: Live::runId()));
        self::assertCount(0, $products->get($config)->wait()->getData()->getData());
        Live::hit(self::CLIENT, 'deleteProduct');
    }

    public function test_every_operation_was_called(): void
    {
        self::assertSame([], Live::missed(self::CLIENT));
    }
}
