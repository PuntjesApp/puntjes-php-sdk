<?php

declare(strict_types=1);

namespace Puntjes\Tests\Unit;

use Puntjes\Exception\ConfigurationException;
use Puntjes\Exception\NotFoundException;
use Puntjes\Exception\RateLimitException;
use Puntjes\Generated\Errors\ErrorCode;
use Puntjes\Generated\Models\CreateTransactionData;
use Puntjes\Generated\Models\SendLoyaltyCardRequestData;
use Puntjes\Tests\Support\TestCase;

final class RequestAdapterTest extends TestCase
{
    public function test_a_json_answer_becomes_the_generated_model(): void
    {
        $this->fake->queueData(['id' => 7, 'external_id' => 'SKU-1', 'name' => 'Koffie']);

        $product = $this->puntjes()->products()->byExternalId('SKU-1')->get()->wait()->getData();

        self::assertSame('Koffie', $product->getName());
        self::assertSame('/api/v1/products/SKU-1', $this->fake->lastRequest()->getUri()->getPath());
    }

    public function test_a_204_returns_null(): void
    {
        $this->fake->queueJson(204, null);

        self::assertNull($this->puntjes()->products()->byExternalId('SKU-1')->delete()->wait());
        self::assertSame('DELETE', $this->fake->lastRequest()->getMethod());
        self::assertSame('', (string) $this->fake->lastRequest()->getBody());
    }

    public function test_an_empty_answer_to_a_model_request_returns_null(): void
    {
        $this->fake->queueJson(204, null);

        self::assertNull($this->puntjes()->products()->byExternalId('SKU-1')->get()->wait());
    }

    public function test_a_202_with_a_body_becomes_the_generated_model(): void
    {
        $this->fake->queueData(['customer_id' => 5, 'channel' => 'email', 'queued' => true], 202);

        $result = $this->puntjes()->customers()->byCustomer('5')->sendCard()->post(new SendLoyaltyCardRequestData)->wait()->getData();

        self::assertTrue($result->getQueued());
        self::assertSame('/api/v1/customers/5/send-card', $this->fake->lastRequest()->getUri()->getPath());
    }

    public function test_a_documented_404_throws_the_typed_not_found(): void
    {
        $this->fake->queueJson(404, ['error' => ['code' => 'PRODUCT_NOT_FOUND', 'message' => 'No product', 'request_id' => 'r-1']]);

        try {
            $this->puntjes()->products()->byExternalId('nope')->get()->wait();
            self::fail('No exception');
        } catch (NotFoundException $e) {
            self::assertSame(ErrorCode::ProductNotFound, $e->errorCode());
            self::assertSame('r-1', $e->requestId());
        }
    }

    public function test_an_unknown_code_keeps_the_family_of_its_status(): void
    {
        $this->fake->queueJson(429, ['error' => ['code' => 'A_CODE_FROM_THE_FUTURE', 'message' => 'x']], ['Retry-After' => '0']);
        $this->fake->queueJson(429, ['error' => ['code' => 'A_CODE_FROM_THE_FUTURE', 'message' => 'x']], ['Retry-After' => '0']);
        $this->fake->queueJson(429, ['error' => ['code' => 'A_CODE_FROM_THE_FUTURE', 'message' => 'x']], ['Retry-After' => '0']);

        try {
            $this->puntjes()->me()->get()->wait();
            self::fail('No exception');
        } catch (RateLimitException $e) {
            self::assertNull($e->errorCode());
            self::assertSame('A_CODE_FROM_THE_FUTURE', $e->code());
        }
    }

    public function test_the_body_is_sent_as_json_with_the_idempotency_key(): void
    {
        $this->fake->queueData(['id' => 1], 201);
        $body = new CreateTransactionData;
        $body->setIdentifier('a@example.test');
        $body->setTotalAmount(1000);
        $body->setIdempotencyKey('k-9');

        $this->puntjes()->transactions()->post($body)->wait();

        $sent = json_decode((string) $this->fake->lastRequest()->getBody(), true);
        self::assertSame('k-9', $sent['idempotency_key']);
        self::assertSame('application/json', $this->fake->lastRequest()->getHeaderLine('Content-Type'));
    }

    public function test_a_raw_url_outside_the_base_url_is_never_sent(): void
    {
        try {
            $this->puntjes()->products()->byExternalId('SKU-1')->withUrl('https://evil.test/api/v1/products/SKU-1')->get()->wait();
            self::fail('No exception');
        } catch (ConfigurationException $e) {
            self::assertStringContainsString('https://evil.test', $e->getMessage());
        }

        self::assertSame(0, $this->fake->requestCount());
    }
}
