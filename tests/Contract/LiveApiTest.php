<?php

declare(strict_types=1);

namespace Puntjes\Tests\Contract;

use PHPUnit\Framework\TestCase;
use Puntjes\Enum\ErrorCode;
use Puntjes\Enum\Period;
use Puntjes\Enum\ProductStatus;
use Puntjes\Exception\ApiException;
use Puntjes\Exception\ConflictException;
use Puntjes\Exception\NotFoundException;
use Puntjes\Model\Branch;
use Puntjes\Puntjes;
use Puntjes\Request\CreateProduct;
use Puntjes\Request\ProductFilters;
use Puntjes\Request\UpdateProduct;
use Puntjes\Request\UpsertProduct;

/**
 * Runs the SDK against a real Puntjes instance.
 *
 * Unit tests prove the SDK behaves correctly against fixtures the SDK's own author
 * wrote — which is exactly the blind spot that lets a wrong assumption about the
 * wire format ship. These tests close it.
 *
 * Skipped unless all three variables are set:
 *
 *     PUNTJES_BASE_URL=http://localhost \
 *     PUNTJES_CLIENT_ID=… PUNTJES_CLIENT_SECRET=… \
 *     vendor/bin/phpunit --testsuite Contract
 *
 * Everything written here is a product with a test-run-unique SKU, deleted in
 * tearDownAfterClass whether or not the tests passed. No customer, transaction or
 * points data is created — those cannot be deleted through the API, and a contract
 * test has no business leaving balances behind in someone's database.
 */
final class LiveApiTest extends TestCase
{
    private static ?Puntjes $client = null;

    /** @var array<int, string> SKUs created by this run, deleted afterwards. */
    private static array $createdSkus = [];

    private static string $prefix = '';

    public static function setUpBeforeClass(): void
    {
        // Unique per run so parallel runs against one instance cannot collide.
        self::$prefix = 'sdk-contract-'.bin2hex(random_bytes(4));
    }

    public static function tearDownAfterClass(): void
    {
        // Unconditional: runs even when a test failed part-way through.
        foreach (self::$createdSkus as $sku) {
            try {
                self::$client?->products->delete($sku);
            } catch (\Throwable) {
                // Already gone, or the run never authenticated. Nothing to undo.
            }
        }

        self::$createdSkus = [];
        self::$client = null;
    }

    private function puntjes(): Puntjes
    {
        $baseUrl = getenv('PUNTJES_BASE_URL') ?: '';
        $clientId = getenv('PUNTJES_CLIENT_ID') ?: '';
        $clientSecret = getenv('PUNTJES_CLIENT_SECRET') ?: '';

        if ($baseUrl === '' || $clientId === '' || $clientSecret === '') {
            self::markTestSkipped('Set PUNTJES_BASE_URL, PUNTJES_CLIENT_ID and PUNTJES_CLIENT_SECRET to run the contract suite.');
        }

        return self::$client ??= Puntjes::make(
            clientId: $clientId,
            clientSecret: $clientSecret,
            baseUrl: $baseUrl,
        );
    }

    /** Register a SKU for cleanup before creating it, so a mid-call failure still tidies up. */
    private function sku(string $suffix): string
    {
        $sku = self::$prefix.'-'.$suffix;
        self::$createdSkus[] = $sku;

        return $sku;
    }

    public function test_the_health_endpoint_answers(): void
    {
        self::assertTrue($this->puntjes()->ping());
    }

    public function test_the_client_credentials_grant_works_end_to_end(): void
    {
        $branding = $this->puntjes()->me();

        self::assertNotSame('', $branding->displayName);
    }

    public function test_an_unknown_customer_maps_to_a_not_found_exception(): void
    {
        try {
            $this->puntjes()->customers->lookup(identifier: 'definitely-not-a-real-card-'.bin2hex(random_bytes(4)));
            self::fail('Expected a NotFoundException.');
        } catch (NotFoundException $e) {
            self::assertSame('CUSTOMER_NOT_FOUND', $e->code());
            self::assertNotNull($e->requestId());
        }
    }

    /**
     * The drift this suite exists for, on the one field a unit fixture cannot police: a
     * type the API sends that this SDK has no case for decodes to null, silently, and the
     * unit fixtures cannot notice because the SDK author wrote them from the same
     * assumption. `rawType` is what separates "the API sent something new" from "the API
     * sent nothing".
     *
     * Read-only, and skipped unless someone names a customer. This suite deliberately
     * creates no customer data, because the API cannot delete it again.
     */
    public function test_every_identifier_the_api_returns_has_a_type_this_sdk_knows(): void
    {
        $identifier = getenv('PUNTJES_CONTRACT_CUSTOMER_IDENTIFIER') ?: '';

        if ($identifier === '') {
            self::markTestSkipped('Set PUNTJES_CONTRACT_CUSTOMER_IDENTIFIER to an existing customer identifier to run this.');
        }

        $customer = $this->puntjes()->customers->lookup(identifier: $identifier);

        self::assertNotSame([], $customer->identifiers, 'The customer carries no identifiers to check.');

        foreach ($customer->identifiers as $each) {
            self::assertNotNull($each->type, "The API sent identifier type '{$each->rawType}', which this SDK has no case for.");
        }
    }

    public function test_find_by_identifier_returns_null_for_an_unknown_card(): void
    {
        self::assertNull(
            $this->puntjes()->customers->findByIdentifier('unknown-'.bin2hex(random_bytes(4))),
        );
    }

    public function test_the_reward_catalogue_decodes(): void
    {
        $rewards = $this->puntjes()->rewards->list();

        foreach ($rewards as $reward) {
            self::assertGreaterThan(0, $reward->pointCost);
            self::assertNotNull($reward->type, 'Unknown reward type on the wire: '.$reward->rawType);

            // Null means redeemable anywhere; a list means limited to those shops. An
            // empty list is a third thing again and must survive the round trip.
            foreach ($reward->branches ?? [] as $branch) {
                self::assertNotSame('', $branch->externalId);
                self::assertNotNull($branch->type, 'Unknown branch type on the wire: '.$branch->rawType);
            }
        }

        self::assertIsArray($rewards);
    }

    public function test_campaigns_paginate(): void
    {
        $page = $this->puntjes()->campaigns->list()->firstPage();

        self::assertGreaterThanOrEqual(1, $page->meta->currentPage);
        self::assertGreaterThanOrEqual(0, $page->meta->total);

        foreach ($page->items as $campaign) {
            // A customer-moment campaign carries none of the three, which is the shape
            // that crashed an SDK typing them as always-present.
            self::assertNotSame('', $campaign->family);

            foreach ($campaign->branches ?? [] as $branch) {
                self::assertNotSame('', $branch->externalId);
            }
        }
    }

    public function test_campaigns_can_be_filtered_to_the_unassigned_bucket(): void
    {
        // `none` is the one branch key valid on every instance, whatever shops the
        // vendor has, so it is the only filter a contract test can assert on.
        $page = $this->puntjes()->campaigns->list(branch: Branch::UNASSIGNED)->firstPage();

        self::assertGreaterThanOrEqual(0, $page->meta->total);
    }

    public function test_statistics_decode_the_full_envelope(): void
    {
        $stats = $this->puntjes()->statistics->get(Period::ThirtyDays, topProductsLimit: 5);

        self::assertSame(Period::ThirtyDays, $stats->period->preset);
        self::assertSame('Europe/Brussels', $stats->period->timezone);
        self::assertNotNull($stats->period->granularity, 'Unknown granularity on the wire.');
        self::assertGreaterThanOrEqual(0, $stats->commerce->orders);
        // Present exactly because this call names no branch.
        self::assertNotNull($stats->loyalty);
        self::assertGreaterThanOrEqual(0, $stats->loyalty->pointsIssued);
    }

    public function test_a_branch_filtered_report_drops_the_loyalty_block(): void
    {
        $stats = $this->puntjes()->statistics->get(Period::ThirtyDays, branch: Branch::UNASSIGNED);

        // Not an omission: neither points liability nor breakage can be attributed to
        // one shop, so the API refuses to print a vendor-wide number under a branch
        // label. An SDK typing this as always-present crashes here.
        self::assertNull($stats->loyalty);
        self::assertGreaterThanOrEqual(0, $stats->commerce->orders);
    }

    public function test_an_unknown_branch_key_is_refused_rather_than_ignored(): void
    {
        try {
            $this->puntjes()->statistics->get(
                Period::ThirtyDays,
                branch: 'sdk-contract-'.bin2hex(random_bytes(4)),
            );
            self::fail('A mistyped branch key must be refused, not answered vendor-wide.');
        } catch (ApiException $e) {
            self::assertSame(ErrorCode::BranchNotFound, $e->errorCode());
            self::assertSame(422, $e->status());
        }
    }

    public function test_an_unissued_voucher_code_is_not_found(): void
    {
        // Verifying is a consume, so the only code a contract test may send is one that
        // cannot exist. This still pins the route, the error code and the status.
        try {
            $this->puntjes()->vouchers->verify('SDKCONTRACT'.bin2hex(random_bytes(4)));
            self::fail('An unissued voucher code must not verify.');
        } catch (NotFoundException $e) {
            self::assertSame('VOUCHER_NOT_FOUND', $e->code());
        }
    }

    public function test_linking_an_unknown_identifier_is_not_found(): void
    {
        // Nothing is written: the customer lookup fails before any link is attempted.
        try {
            $this->puntjes()->customers->linkExternalId(
                'sdk-contract-'.bin2hex(random_bytes(4)),
                'sdk-contract-'.bin2hex(random_bytes(4)),
            );
            self::fail('Linking an unknown identifier must not succeed.');
        } catch (NotFoundException $e) {
            self::assertSame('CUSTOMER_NOT_FOUND', $e->code());
        }
    }

    public function test_sending_a_card_to_an_unknown_external_id_is_not_found(): void
    {
        // A random external id cannot collide with a real customer, so no mail can be
        // queued by this test.
        try {
            $this->puntjes()->customers->sendCardByExternalId('sdk-contract-'.bin2hex(random_bytes(4)));
            self::fail('Sending a card to an unknown external id must not succeed.');
        } catch (NotFoundException $e) {
            self::assertNotNull($e->requestId());
        }
    }

    public function test_the_product_lifecycle(): void
    {
        $products = $this->puntjes()->products;
        $sku = $this->sku('lifecycle');

        $created = $products->create(new CreateProduct(
            externalId: $sku,
            name: 'SDK contract test product',
            priceCents: 250,
            category: 'SDK Tests',
            stock: 10,
        ));

        self::assertSame($sku, $created->externalId);
        self::assertSame(250, $created->priceCents);
        self::assertSame(ProductStatus::Active, $created->status);

        // Creating the same SKU again is a 409, distinguishable from other failures.
        try {
            $products->create(new CreateProduct(externalId: $sku, name: 'Duplicate'));
            self::fail('Expected a ConflictException.');
        } catch (ConflictException $e) {
            self::assertSame('PRODUCT_EXTERNAL_ID_DUPLICATE', $e->code());
        }

        self::assertSame('SDK contract test product', $products->find($sku)->name);

        // PATCH changes only what is named.
        $patched = $products->update($sku, new UpdateProduct(priceCents: 275));
        self::assertSame(275, $patched->priceCents);
        self::assertSame('SDK contract test product', $patched->name);
        self::assertSame('SDK Tests', $patched->category);

        // PUT replaces: the omitted category must come back cleared.
        $upserted = $products->upsert($sku, new UpsertProduct(name: 'Replaced', priceCents: 300));
        self::assertFalse($upserted['created'], 'An existing SKU must upsert as an update, not a create.');
        self::assertSame('Replaced', $upserted['product']->name);
        self::assertNull($upserted['product']->category);

        $products->delete($sku);

        self::assertNull($products->findOrNull($sku));
    }

    public function test_upserting_a_new_sku_reports_it_as_created(): void
    {
        $sku = $this->sku('upsert-new');

        $result = $this->puntjes()->products->upsert($sku, new UpsertProduct(
            name: 'SDK contract upsert',
            priceCents: 100,
        ));

        self::assertTrue($result['created'], 'A brand-new SKU must upsert as a create (HTTP 201).');
    }

    public function test_a_batch_upsert_reports_per_item_outcomes(): void
    {
        $good = $this->sku('batch-ok');
        $bad = $this->sku('batch-bad');

        $result = $this->puntjes()->products->batchUpsert([
            $good => new UpsertProduct(name: 'SDK batch product', priceCents: 150),
            // An empty name fails validation for this item alone; the batch still answers 200.
            $bad => new UpsertProduct(name: ''),
        ]);

        self::assertSame(2, $result->total);
        self::assertSame(1, $result->failed);
        self::assertTrue($result->hasFailures());
        self::assertSame($bad, $result->failures()[0]->externalId);
        self::assertArrayHasKey('name', $result->failures()[0]->errors);
    }

    public function test_listing_products_filters_and_paginates(): void
    {
        $sku = $this->sku('listed');
        $this->puntjes()->products->upsert($sku, new UpsertProduct(
            name: 'SDK listable product',
            category: 'SDK Tests',
        ));

        $matches = $this->puntjes()->products
            ->list(new ProductFilters(search: 'SDK listable', perPage: 5))
            ->all();

        $skus = array_map(static fn ($product): string => $product->externalId, $matches);

        self::assertContains($sku, $skus);
    }

    public function test_an_unknown_sku_is_a_not_found_exception(): void
    {
        try {
            $this->puntjes()->products->find('missing-'.bin2hex(random_bytes(4)));
            self::fail('Expected a NotFoundException.');
        } catch (NotFoundException $e) {
            self::assertSame('PRODUCT_NOT_FOUND', $e->code());
        }
    }
}
