<?php

declare(strict_types=1);

namespace Puntjes\Tests\Unit;

use Puntjes\Exception\TransportException;
use Puntjes\Generated\Products\ProductsRequestBuilderGetRequestConfiguration;
use Puntjes\IdempotencyKey;
use Puntjes\Pagination\Paginator;
use Puntjes\Tests\Support\TestCase;
use Puntjes\WalletPass;

final class HelpersTest extends TestCase
{
    public function test_the_apple_pass_comes_back_as_the_pkpass_bytes(): void
    {
        $this->fake->queueRaw(200, "PK\x03\x04pass", ['Content-Type' => 'application/vnd.apple.pkpass']);

        $pass = WalletPass::apple($this->puntjes(), 42);

        self::assertSame("PK\x03\x04pass", (string) $pass);
        self::assertSame('platform=apple', $this->fake->lastRequest()->getUri()->getQuery());
        self::assertSame('application/vnd.apple.pkpass', $this->fake->lastRequest()->getHeaderLine('Accept'));
    }

    public function test_the_paginator_walks_every_page(): void
    {
        foreach ([1, 2] as $page) {
            $this->fake->queueData(['data' => [['external_id' => "SKU-{$page}", 'name' => 'x']], 'links' => [], 'meta' => ['current_page' => $page, 'last_page' => 2, 'per_page' => 1, 'total' => 2]]);
        }
        $client = $this->puntjes();

        $ids = [];
        foreach (Paginator::of(fn (int $page) => $client->products()->get(new ProductsRequestBuilderGetRequestConfiguration(queryParameters: ProductsRequestBuilderGetRequestConfiguration::createQueryParameters(page: $page)))->wait()) as $product) {
            $ids[] = $product->getExternalId();
        }

        self::assertSame(['SKU-1', 'SKU-2'], $ids);
    }

    public function test_the_paginator_keeps_every_item_when_collected_into_an_array(): void
    {
        foreach ([1, 2] as $page) {
            $this->fake->queueData(['data' => [['external_id' => "SKU-{$page}", 'name' => 'x']], 'links' => [], 'meta' => ['current_page' => $page, 'last_page' => 2, 'per_page' => 1, 'total' => 2]]);
        }
        $client = $this->puntjes();

        $products = iterator_to_array(Paginator::of(fn (int $page) => $client->products()->get(new ProductsRequestBuilderGetRequestConfiguration(queryParameters: ProductsRequestBuilderGetRequestConfiguration::createQueryParameters(page: $page)))->wait()));

        self::assertCount(2, $products);
    }

    public function test_an_idempotency_key_is_a_fresh_uuid(): void
    {
        self::assertMatchesRegularExpression('/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/', IdempotencyKey::new());
        self::assertNotSame(IdempotencyKey::new(), IdempotencyKey::new());
    }

    public function test_an_empty_apple_pass_throws_a_transport_exception(): void
    {
        $this->fake->queueRaw(200, '', ['Content-Type' => 'application/vnd.apple.pkpass']);

        $this->expectException(TransportException::class);

        WalletPass::apple($this->puntjes(), 42);
    }
}
