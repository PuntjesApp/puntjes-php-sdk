<?php

declare(strict_types=1);

namespace Puntjes\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Puntjes\Tests\Contract\Support\RequiredFields;

final class RequiredFieldsTest extends TestCase
{
    private RequiredFields $fields;

    protected function setUp(): void
    {
        $this->fields = new RequiredFields([
            'paths' => [
                '/me' => ['get' => ['responses' => ['200' => self::json(self::envelope(['$ref' => '#/components/schemas/Branding']))]]],
                '/products' => ['get' => ['responses' => ['200' => self::json(self::envelope([
                    'type' => 'array',
                    'items' => ['$ref' => '#/components/schemas/Product'],
                ]))]]],
                '/products/{externalId}' => [
                    'get' => ['responses' => ['200' => self::json(self::envelope(['$ref' => '#/components/schemas/Product']))]],
                    'delete' => ['responses' => ['204' => ['description' => '']]],
                ],
                '/campaigns/{id}' => ['get' => ['responses' => ['200' => self::json(self::envelope([
                    'oneOf' => [['$ref' => '#/components/schemas/Points'], ['$ref' => '#/components/schemas/Moment']],
                ]))]]],
                '/gifts/{id}' => ['get' => ['responses' => ['200' => self::json(self::envelope([
                    'oneOf' => [['$ref' => '#/components/schemas/PointsGift'], ['$ref' => '#/components/schemas/ProductGift']],
                    'discriminator' => ['propertyName' => 'type', 'mapping' => [
                        'points' => '#/components/schemas/PointsGift',
                        'product' => '#/components/schemas/ProductGift',
                    ]],
                ]))]]],
            ],
            'components' => ['schemas' => [
                'Branding' => ['type' => 'object', 'properties' => ['display_name' => ['type' => 'string'], 'logo_url' => ['type' => ['string', 'null']]], 'required' => ['display_name', 'logo_url']],
                'Product' => ['type' => 'object', 'properties' => ['name' => ['type' => 'string'], 'category' => ['$ref' => '#/components/schemas/Category']], 'required' => ['name', 'category']],
                'Category' => ['type' => ['object', 'null'], 'properties' => ['slug' => ['type' => 'string']], 'required' => ['slug']],
                'Points' => ['type' => 'object', 'required' => ['points_per_euro']],
                'Moment' => ['type' => 'object', 'required' => ['moment']],
                'PointsGift' => ['type' => 'object', 'required' => ['type', 'points']],
                'ProductGift' => ['type' => 'object', 'required' => ['type', 'product']],
            ]],
        ]);
    }

    public function test_an_answer_with_every_required_field_has_nothing_missing(): void
    {
        self::assertSame([], $this->fields->missing('GET', '/me', 200, ['data' => ['display_name' => 'Bakkerij', 'logo_url' => null]]));
    }

    public function test_a_removed_field_is_named_by_its_path_in_the_answer(): void
    {
        self::assertSame(['GET /me: data.display_name'], $this->fields->missing('GET', '/me', 200, ['data' => ['logo_url' => null]]));
    }

    public function test_a_null_required_field_is_present(): void
    {
        self::assertSame([], $this->fields->missing('GET', '/products/sku-1', 200, ['data' => ['name' => 'Brood', 'category' => null]]));
    }

    public function test_a_path_parameter_matches_its_template(): void
    {
        self::assertSame(['GET /products/sku-1: data.category.slug'], $this->fields->missing('GET', '/products/sku-1', 200, ['data' => ['name' => 'Brood', 'category' => []]]));
    }

    public function test_every_item_of_a_list_is_checked(): void
    {
        $answer = ['data' => [['name' => 'Brood', 'category' => null], ['category' => null]]];

        self::assertSame(['GET /products: data[1].name'], $this->fields->missing('GET', '/products', 200, $answer));
    }

    public function test_a_union_is_satisfied_by_one_branch(): void
    {
        self::assertSame([], $this->fields->missing('GET', '/campaigns/7', 200, ['data' => ['moment' => 'birthday']]));
    }

    public function test_a_union_no_branch_satisfies_is_reported(): void
    {
        self::assertSame(['GET /campaigns/7: data matches no branch of its oneOf'], $this->fields->missing('GET', '/campaigns/7', 200, ['data' => ['name' => 'x']]));
    }

    public function test_a_discriminator_picks_the_branch_the_answer_names(): void
    {
        self::assertSame(['GET /gifts/3: data.points'], $this->fields->missing('GET', '/gifts/3', 200, ['data' => ['type' => 'points', 'product' => 'Brood']]));
    }

    public function test_an_answer_with_no_json_schema_is_not_checked(): void
    {
        self::assertSame([], $this->fields->missing('DELETE', '/products/sku-1', 204, null));
    }

    public function test_an_operation_the_spec_does_not_list_is_reported(): void
    {
        self::assertSame(['GET /unknown: no operation in spec/api.json'], $this->fields->missing('GET', '/unknown', 200, ['data' => []]));
    }

    /** @param array<string, mixed> $data */
    private static function envelope(array $data): array
    {
        return ['type' => 'object', 'properties' => ['data' => $data], 'required' => ['data']];
    }

    /** @param array<string, mixed> $schema */
    private static function json(array $schema): array
    {
        return ['description' => '', 'content' => ['application/json' => ['schema' => $schema]]];
    }
}
