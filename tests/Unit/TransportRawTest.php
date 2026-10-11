<?php

declare(strict_types=1);

namespace Puntjes\Tests\Unit;

use Puntjes\Auth\ClientCredentialsProvider;
use Puntjes\Auth\InMemoryTokenStore;
use Puntjes\Exception\AuthenticationException;
use Puntjes\Exception\ServerException;
use Puntjes\Http\HttpClient;
use Puntjes\Http\Transport;
use Puntjes\Tests\Support\TestCase;

final class TransportRawTest extends TestCase
{
    private function transport(): Transport
    {
        $http = new HttpClient($this->fake);

        return new Transport($this->config(), $http, new ClientCredentialsProvider($this->config(), $http, new InMemoryTokenStore), fn (float $s) => $this->sleeps[] = $s);
    }

    public function test_a_raw_post_with_an_idempotency_key_is_retried_with_the_same_body(): void
    {
        $this->fake->queueJson(503, ['error' => ['code' => 'INTERNAL_ERROR', 'message' => 'x']]);
        $this->fake->queueData(['ok' => true], 201);

        $body = '{"idempotency_key":"k-1","total_amount":1000}';
        $response = $this->transport()->sendRaw('POST', 'https://app.puntjes.test/api/v1/transactions', ['Content-Type' => 'application/json'], $body);

        self::assertSame(201, $response->status);
        $posts = $this->fake->requestsTo('/api/v1/transactions');
        self::assertCount(2, $posts);
        self::assertSame($body, (string) $posts[1]->getBody());
    }

    public function test_a_raw_post_without_an_idempotency_key_is_never_retried(): void
    {
        $this->fake->queueJson(503, ['error' => ['code' => 'INTERNAL_ERROR', 'message' => 'x']]);

        $this->expectException(ServerException::class);

        try {
            $this->transport()->sendRaw('POST', 'https://app.puntjes.test/api/v1/redemptions/ABC/verify', [], null);
        } finally {
            self::assertCount(1, $this->fake->requestsTo('/api/v1/redemptions/ABC/verify'));
        }
    }

    public function test_a_caller_content_type_is_kept_and_not_duplicated(): void
    {
        $this->fake->queueData(['ok' => true]);

        $this->transport()->sendRaw('PUT', 'https://app.puntjes.test/api/v1/products/A', ['content-type' => 'application/x-test'], '{}');

        $request = $this->fake->requestsTo('/api/v1/products/A')[0];
        self::assertSame(['application/x-test'], $request->getHeader('Content-Type'));
    }

    public function test_an_unauthenticated_call_maps_a_401_without_a_grant(): void
    {
        $this->fake->queueJson(401, ['error' => ['code' => 'UNAUTHENTICATED', 'message' => 'x']]);

        $this->expectException(AuthenticationException::class);

        try {
            $this->transport()->sendRaw('GET', 'https://app.puntjes.test/api/v1/health', [], null, authenticate: false);
        } finally {
            self::assertSame(1, $this->fake->requestCount());
            self::assertFalse($this->fake->requestAt(0)->hasHeader('Authorization'));
        }
    }

    public function test_an_unauthenticated_call_keeps_the_retry_rule(): void
    {
        $this->fake->queueJson(503, ['error' => ['code' => 'INTERNAL_ERROR', 'message' => 'x']]);
        $this->fake->queueData(['status' => 'ok']);

        $response = $this->transport()->sendRaw('GET', 'https://app.puntjes.test/api/v1/health', [], null, authenticate: false);

        self::assertSame(200, $response->status);
        self::assertSame(2, $this->fake->requestCount());
        self::assertSame([0.5], $this->sleeps);
    }
}
