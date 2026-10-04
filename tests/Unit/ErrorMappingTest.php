<?php

declare(strict_types=1);

namespace Puntjes\Tests\Unit;

use Puntjes\Enum\ErrorCode;
use Puntjes\Exception\ApiException;
use Puntjes\Exception\AuthenticationException;
use Puntjes\Exception\ConflictException;
use Puntjes\Exception\ForbiddenException;
use Puntjes\Exception\NotFoundException;
use Puntjes\Exception\PlanLimitExceededException;
use Puntjes\Exception\RateLimitException;
use Puntjes\Exception\ServerException;
use Puntjes\Exception\TransportException;
use Puntjes\Exception\ValidationException;
use Puntjes\Request\ProductFilters;
use Puntjes\Request\UpsertProduct;
use Puntjes\Tests\Support\TestCase;

final class ErrorMappingTest extends TestCase
{
    /**
     * @return array<string, array{int, string, class-string}>
     */
    public static function statusProvider(): array
    {
        return [
            '400 unreadable body' => [400, 'INVALID_JSON', ApiException::class],
            '401 unauthenticated' => [401, 'UNAUTHENTICATED', AuthenticationException::class],
            '403 vendor suspended' => [403, 'VENDOR_SUSPENDED', ForbiddenException::class],
            '404 customer' => [404, 'CUSTOMER_NOT_FOUND', NotFoundException::class],
            '404 unreadable path' => [404, 'ROUTE_NOT_FOUND', NotFoundException::class],
            '409 duplicate identifier' => [409, 'IDENTIFIER_DUPLICATE', ConflictException::class],
            '422 domain refusal' => [422, 'INSUFFICIENT_BALANCE', ApiException::class],
            '422 idempotency conflict' => [422, 'IDEMPOTENCY_KEY_CONFLICT', ApiException::class],
            '422 email suppressed' => [422, 'CUSTOMER_EMAIL_SUPPRESSED', ApiException::class],
            '422 validation' => [422, 'VALIDATION_ERROR', ValidationException::class],
            '429 rate limited' => [429, 'RATE_LIMITED', RateLimitException::class],
            '429 plan limit' => [429, 'PLAN_LIMIT_EXCEEDED', PlanLimitExceededException::class],
            '500 internal' => [500, 'INTERNAL_ERROR', ServerException::class],
        ];
    }

    /**
     * @dataProvider statusProvider
     *
     * @param  class-string  $expected
     */
    public function test_it_maps_status_and_code_to_a_typed_exception(int $status, string $code, string $expected): void
    {
        // Queued twice: a 401 costs two attempts, because the transport re-grants a
        // token and replays once before deciding the credentials are genuinely bad.
        // Every other status consumes just the first.
        $this->fake->queueError($status, $code, 'Nope.');
        $this->fake->queueError($status, $code, 'Nope.');

        $puntjes = $this->puntjes(maxRetries: 0);

        try {
            $puntjes->me();
            self::fail("Expected {$expected}.");
        } catch (ApiException $e) {
            self::assertSame($expected, $e::class);
            self::assertSame($status, $e->status());
            self::assertSame($code, $e->code());
            self::assertSame('req_'.$code, $e->requestId());
        }
    }

    public function test_a_body_the_api_cannot_read_is_a_client_error_and_is_never_replayed(): void
    {
        // The JSON was cut off or is not valid UTF-8. The API created and sent nothing,
        // and the same body can only fail the same way, so the SDK must not try again.
        // A PUT is a call the SDK normally replays, which makes it the sharp test.
        $this->fake->queueError(400, 'INVALID_JSON', 'The request body is not valid JSON.');

        try {
            $this->puntjes()->products->upsert('SKU-1', new UpsertProduct(name: 'Brood'));
            self::fail('Expected an ApiException.');
        } catch (ApiException $e) {
            self::assertSame(ApiException::class, $e::class);
            self::assertTrue($e->is(ErrorCode::InvalidJson));
            self::assertSame(ErrorCode::InvalidJson, $e->errorCode());
            self::assertSame(400, $e->status());
        }

        self::assertSame(1, $this->fake->apiRequestCount());
        self::assertSame([], $this->sleeps);
    }

    public function test_text_the_api_cannot_read_in_a_query_value_is_a_validation_error_naming_the_field(): void
    {
        // A lone %FF byte in `search` used to answer 500. It now answers 422 with the
        // field named, and it lands on the type an integrator already catches.
        $this->fake->queueError(422, 'VALIDATION_ERROR', 'The given data was invalid.', details: [
            'search' => ['The search field must be valid UTF-8 text.'],
        ]);

        try {
            $this->puntjes()->products->list(new ProductFilters(search: "\xFF"))->firstPage();
            self::fail('Expected a ValidationException.');
        } catch (ValidationException $e) {
            self::assertSame(422, $e->status());
            self::assertSame(['The search field must be valid UTF-8 text.'], $e->errorsFor('search'));
        }

        self::assertSame(1, $this->fake->apiRequestCount());
    }

    public function test_a_path_the_api_cannot_read_is_a_not_found_error(): void
    {
        // A NUL byte in a key used to cut the key short and hit the wrong record. The
        // API now answers 404 ROUTE_NOT_FOUND, so no record is touched.
        $this->fake->queueError(404, 'ROUTE_NOT_FOUND', 'The route could not be found.');

        try {
            $this->puntjes()->products->find("SKU-1\0");
            self::fail('Expected a NotFoundException.');
        } catch (NotFoundException $e) {
            self::assertTrue($e->is(ErrorCode::RouteNotFound));
            self::assertSame(404, $e->status());
        }

        self::assertSame(
            '/api/v1/products/SKU-1%00',
            $this->fake->requestAt(1)->getUri()->getPath(),
        );
    }

    public function test_a_domain_refusal_is_not_a_validation_exception(): void
    {
        // 422 covers both. Catching ValidationException must not swallow a business
        // refusal that carries no field errors and needs different handling.
        $this->fake->queueError(422, 'OUT_OF_STOCK', 'This reward is out of stock.');

        $puntjes = $this->puntjes();

        try {
            $puntjes->me();
            self::fail('Expected an ApiException.');
        } catch (ApiException $e) {
            self::assertNotInstanceOf(ValidationException::class, $e);
            self::assertTrue($e->is(ErrorCode::OutOfStock));
        }
    }

    public function test_validation_errors_expose_the_field_map(): void
    {
        $this->fake->queueError(422, 'VALIDATION_ERROR', 'The given data was invalid.', details: [
            'total_amount' => ['The total amount field is required.'],
            'identifier' => ['The identifier field is required.'],
        ]);

        $puntjes = $this->puntjes();

        try {
            $puntjes->me();
            self::fail('Expected a ValidationException.');
        } catch (ValidationException $e) {
            self::assertSame(['The total amount field is required.'], $e->errorsFor('total_amount'));
            self::assertCount(2, $e->messages());
            self::assertSame([], $e->errorsFor('nonexistent'));
        }
    }

    public function test_an_unknown_error_code_still_surfaces_its_raw_value(): void
    {
        // Forward compatibility: a newer API must not break an older SDK.
        $this->fake->queueError(422, 'SOME_FUTURE_CODE', 'Something new.');

        $puntjes = $this->puntjes();

        try {
            $puntjes->me();
            self::fail('Expected an ApiException.');
        } catch (ApiException $e) {
            self::assertSame('SOME_FUTURE_CODE', $e->code());
            self::assertNull($e->errorCode());
        }
    }

    public function test_a_non_json_4xx_error_page_is_a_transport_problem_not_an_api_error(): void
    {
        // A WAF block page or login redirect answering HTML means the request never
        // reached Puntjes. Inventing an error code for it would send integrators
        // hunting the wrong bug — and it must never be retried.
        $this->fake->queueRaw(403, '<html><body>Access denied</body></html>', ['Content-Type' => 'text/html']);

        $puntjes = $this->puntjes(maxRetries: 0);

        $this->expectException(TransportException::class);
        $this->expectExceptionMessage('non-JSON error response');

        $puntjes->me();
    }

    public function test_a_non_json_5xx_maps_to_a_server_error(): void
    {
        // An HTML 502 from a load balancer is a failing backend, not a wrong host —
        // it must carry the retryable ServerException type, with a synthetic code
        // that can never be mistaken for one the API emits.
        $this->fake->queueRaw(502, '<html><body>Bad Gateway</body></html>', ['Content-Type' => 'text/html']);

        $puntjes = $this->puntjes(maxRetries: 0);

        try {
            $puntjes->me();
            self::fail('Expected a ServerException.');
        } catch (ServerException $e) {
            self::assertSame(502, $e->status());
            self::assertSame('NON_JSON_RESPONSE', $e->code());
            self::assertNull($e->errorCode());
            self::assertStringContainsString('Bad Gateway', $e->getMessage());
        }
    }

    public function test_a_success_body_without_the_data_envelope_is_rejected(): void
    {
        $this->fake->queueJson(200, ['unexpected' => 'shape']);

        $puntjes = $this->puntjes();

        $this->expectException(TransportException::class);
        $this->expectExceptionMessage('no "data" envelope');

        $puntjes->me();
    }

    public function test_retry_after_accepts_an_http_date(): void
    {
        $this->fake->queueError(429, 'RATE_LIMITED', 'Slow down.', headers: [
            'Retry-After' => gmdate('D, d M Y H:i:s \G\M\T', time() + 60),
        ]);

        $puntjes = $this->puntjes(maxRetries: 0);

        try {
            $puntjes->me();
            self::fail('Expected a RateLimitException.');
        } catch (RateLimitException $e) {
            self::assertNotNull($e->retryAfter());
            self::assertGreaterThan(50, $e->retryAfter());
            self::assertLessThanOrEqual(60, $e->retryAfter());
        }
    }
}
