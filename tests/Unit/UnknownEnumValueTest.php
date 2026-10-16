<?php

declare(strict_types=1);

namespace Puntjes\Tests\Unit;

use GuzzleHttp\Psr7\Utils;
use Puntjes\Auth\ClientCredentialsProvider;
use Puntjes\Auth\InMemoryTokenStore;
use Puntjes\Generated\Models\DiscountRedemptionData;
use Puntjes\Generated\Models\RewardCatalogItemData_discount_type;
use Puntjes\Http\HttpClient;
use Puntjes\Http\Psr18RequestAdapter;
use Puntjes\Http\Transport;
use Puntjes\Tests\Support\RedemptionAnswer;
use Puntjes\Tests\Support\TestCase;

/**
 * An enum value from a newer API reads as null, and the rest of the answer parses.
 */
final class UnknownEnumValueTest extends TestCase
{
    public function test_an_unknown_enum_value_reads_as_null_and_the_rest_of_the_list_parses(): void
    {
        $this->fake->queueData([
            ['id' => 1, 'name' => 'OK', 'type' => 'discount', 'point_cost' => 10, 'payment_amount' => 0, 'discount_type' => 'percentage', 'discount_value' => 10, 'branches' => null, 'product_reference' => null, 'is_unlimited' => true],
            ['id' => 2, 'name' => 'NEW', 'type' => 'discount', 'point_cost' => 10, 'payment_amount' => 0, 'discount_type' => 'buy_one_get_one', 'discount_value' => 1],
        ]);

        $rewards = $this->puntjes()->rewards()->get()->wait()->getData();

        self::assertCount(2, $rewards);
        self::assertSame('percentage', $rewards[0]->getDiscountType()?->value());
        self::assertNull($rewards[1]->getDiscountType());
        self::assertSame('NEW', $rewards[1]->getName());
        self::assertSame(10, $rewards[1]->getPointCost());
        self::assertSame(1, $rewards[1]->getDiscountValue());
        self::assertSame('buy_one_get_one', $rewards[1]->getAdditionalData()['discount_type'] ?? null);
        self::assertArrayNotHasKey('discount_type', $rewards[0]->getAdditionalData() ?? []);
    }

    public function test_an_unknown_enum_value_inside_type_specific_data_reads_as_null(): void
    {
        $this->fake->queueData(RedemptionAnswer::lookup(['type' => 'discount', 'discount_type' => 'buy_one_get_one', 'discount_value' => 1, 'product_reference' => null]));

        $discount = $this->puntjes()->redemptions()->byCode('ABC123')->get()->wait()->getData()->getTypeSpecificData()->getDiscountRedemptionData();

        self::assertInstanceOf(DiscountRedemptionData::class, $discount);
        self::assertNull($discount->getDiscountType());
        self::assertSame('discount', $discount->getType()?->value());
        self::assertSame(1, $discount->getDiscountValue());
        self::assertSame('buy_one_get_one', $discount->getAdditionalData()['discount_type'] ?? null);
    }

    public function test_an_unknown_value_inside_a_collection_of_enums_is_left_out(): void
    {
        $node = (new Psr18RequestAdapter($this->transport(), 'https://app.puntjes.test/api/v1'))
            ->getParseNodeFactory()
            ->getRootParseNode('application/json', Utils::streamFor('["percentage","buy_one_get_one","fixed_amount"]'));

        $values = array_map(fn (RewardCatalogItemData_discount_type $type) => $type->value(), $node->getCollectionOfEnumValues(RewardCatalogItemData_discount_type::class));

        self::assertSame(['percentage', 'fixed_amount'], array_values($values));
    }

    private function transport(): Transport
    {
        $http = new HttpClient($this->fake);

        return new Transport($this->config(), $http, new ClientCredentialsProvider($this->config(), $http, new InMemoryTokenStore));
    }
}
