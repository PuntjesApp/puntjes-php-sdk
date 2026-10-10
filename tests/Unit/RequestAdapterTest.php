<?php

declare(strict_types=1);

namespace Puntjes\Tests\Unit;

use Puntjes\Exception\ConfigurationException;
use Puntjes\Exception\NotFoundException;
use Puntjes\Exception\RateLimitException;
use Puntjes\Exception\TransportException;
use Puntjes\Generated\Errors\ErrorCode;
use Puntjes\Generated\Models\CreateTransactionData;
use Puntjes\Generated\Models\SendLoyaltyCardRequestData;
use Puntjes\Generated\Models\UpdateCustomerData;
use Puntjes\Generated\Models\UpdateProductData;
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

    public function test_a_2xx_html_page_throws_a_transport_exception(): void
    {
        $this->fake->queueRaw(200, '<html>Login</html>', ['Content-Type' => 'text/html; charset=utf-8']);

        $this->expectException(TransportException::class);
        $this->expectExceptionMessage('<html>Login</html>');

        $this->puntjes()->me()->get()->wait();
    }

    public function test_a_2xx_with_broken_json_throws_a_transport_exception(): void
    {
        $this->fake->queueRaw(200, '{"data": {', ['Content-Type' => 'application/json']);

        $this->expectException(TransportException::class);

        $this->puntjes()->me()->get()->wait();
    }

    public function test_the_media_type_is_read_without_case_or_spaces(): void
    {
        $this->fake->queueRaw(200, '{"data": {"external_id": "SKU-1", "name": "Koffie"}}', ['Content-Type' => ' Application/JSON ; charset=utf-8']);

        self::assertSame('Koffie', $this->puntjes()->products()->byExternalId('SKU-1')->get()->wait()?->getData()?->getName());
    }

    public function test_a_json_body_with_no_content_type_is_read_as_json(): void
    {
        $this->fake->queueRaw(200, '{"data": {"external_id": "SKU-1", "name": "Koffie"}}');

        self::assertSame('Koffie', $this->puntjes()->products()->byExternalId('SKU-1')->get()->wait()?->getData()?->getName());
    }

    public function test_a_non_json_body_with_no_content_type_throws_a_transport_exception(): void
    {
        $this->fake->queueRaw(200, 'Service Unavailable');

        $this->expectException(TransportException::class);

        $this->puntjes()->me()->get()->wait();
    }

    public function test_a_null_setter_sends_nothing_and_a_null_in_additional_data_clears_the_field(): void
    {
        $this->fake->queueData(['id' => 5]);
        $this->fake->queueData(['id' => 5]);
        $body = new UpdateCustomerData;
        $body->setFirstName('A');
        $body->setPhone(null);
        $client = $this->puntjes();

        $client->customers()->byExternalId()->byExternalId('C-1')->patch($body)->wait();
        self::assertSame('{"first_name":"A"}', (string) $this->fake->lastRequest()->getBody());

        $body->setAdditionalData(['phone' => null]);
        $client->customers()->byExternalId()->byExternalId('C-1')->patch($body)->wait();
        self::assertSame('{"first_name":"A","phone":null}', (string) $this->fake->lastRequest()->getBody());
    }

    public function test_a_null_in_additional_data_clears_a_product_field(): void
    {
        $this->fake->queueData(['external_id' => 'SKU-1', 'name' => 'Koffie']);
        $body = new UpdateProductData;
        $body->setAdditionalData(['description' => null]);

        $this->puntjes()->products()->byExternalId('SKU-1')->patch($body)->wait();

        self::assertSame('{"description":null}', (string) $this->fake->lastRequest()->getBody());
    }
}
