<?php

declare(strict_types=1);

namespace Puntjes\Tests\Unit;

use Puntjes\Exception\ApiException;
use Puntjes\Exception\PlanLimitExceededException;
use Puntjes\Exception\RateLimitException;
use Puntjes\Exception\ServerException;
use Puntjes\Exception\TransportException;
use Puntjes\Request\AdjustWallet;
use Puntjes\Request\CreateCustomer;
use Puntjes\Request\CreateIdentifier;
use Puntjes\Request\SubmitTransaction;
use Puntjes\Tests\Support\TestCase;

/**
 * The retry rules are the SDK's most safety-critical behaviour: retrying the wrong
 * POST double-charges a customer's points. These lock the policy down.
 */
final class RetryTest extends TestCase
{
    public function test_a_get_is_retried_after_a_server_error(): void
    {
        $this->fake->queueError(500, 'INTERNAL_ERROR');
        $this->fake->queueData(['display_name' => 'Bakkerij Jan']);

        $branding = $this->puntjes()->me();

        self::assertSame('Bakkerij Jan', $branding->displayName);
        self::assertSame([0.5], $this->sleeps);
    }

    public function test_a_get_is_retried_after_a_connection_failure(): void
    {
        $this->fake->queueNetworkFailure();
        $this->fake->queueData(['display_name' => 'Bakkerij Jan']);

        self::assertSame('Bakkerij Jan', $this->puntjes()->me()->displayName);
    }

    public function test_retries_back_off_exponentially_and_then_give_up(): void
    {
        $this->fake->queueError(500, 'INTERNAL_ERROR');
        $this->fake->queueError(500, 'INTERNAL_ERROR');
        $this->fake->queueError(500, 'INTERNAL_ERROR');

        $this->expectException(ServerException::class);

        try {
            $this->puntjes(maxRetries: 2)->me();
        } finally {
            self::assertSame([0.5, 1.0], $this->sleeps);
        }
    }

    public function test_a_rate_limit_is_retried_after_the_server_specified_delay(): void
    {
        $this->fake->queueError(429, 'RATE_LIMITED', 'Too many requests.', headers: ['Retry-After' => '7']);
        $this->fake->queueData(['display_name' => 'Bakkerij Jan']);

        $this->puntjes()->me();

        self::assertSame([7.0], $this->sleeps);
    }

    public function test_a_plan_limit_is_never_retried_despite_sharing_the_429_status(): void
    {
        $this->fake->queueError(429, 'PLAN_LIMIT_EXCEEDED', 'Transaction limit exceeded for your subscription plan.');

        $puntjes = $this->puntjes();

        $this->expectException(PlanLimitExceededException::class);

        try {
            $puntjes->transactions->submit(new SubmitTransaction(identifier: 'CARD-1', totalAmount: 4200));
        } finally {
            // Token grant + the one attempt. No retry: waiting cannot clear a billing stop.
            self::assertSame(2, $this->fake->requestCount());
            self::assertSame([], $this->sleeps);
        }
    }

    public function test_a_post_carrying_an_idempotency_key_is_retried(): void
    {
        $this->fake->queueError(500, 'INTERNAL_ERROR');
        $this->fake->queueData([
            'id' => 1,
            'customer_id' => 9,
            'idempotency_key' => 'order-77',
            'total_amount' => 4200,
            'created_at' => '2026-07-30T10:00:00+00:00',
            'points_earned' => 42,
            'rules_applied' => [],
            'items' => [],
        ], 201);

        $transaction = $this->puntjes()->transactions->submit(new SubmitTransaction(
            identifier: 'CARD-1',
            totalAmount: 4200,
            idempotencyKey: 'order-77',
        ));

        self::assertSame(42, $transaction->pointsEarned);
        self::assertSame([0.5], $this->sleeps);
    }

    public function test_a_retried_post_reuses_the_same_idempotency_key(): void
    {
        $this->fake->queueError(500, 'INTERNAL_ERROR');
        $this->fake->queueData(['id' => 1, 'wallet_id' => 2, 'type' => 'adjust', 'amount' => 50, 'running_balance' => 150, 'created_at' => '2026-07-30T10:00:00+00:00']);

        // No key supplied — the SDK generates one, and reusing it across the retry is
        // exactly what stops the replay from moving the balance twice.
        $this->puntjes()->wallets->adjust(9, new AdjustWallet(50, 'Goodwill'));

        $first = $this->fake->bodyAt(1)['idempotency_key'] ?? null;
        $second = $this->fake->bodyAt(2)['idempotency_key'] ?? null;

        self::assertIsString($first);
        self::assertNotSame('', $first);
        self::assertSame($first, $second);
    }

    public function test_a_post_without_an_idempotency_key_is_never_retried(): void
    {
        $this->fake->queueError(500, 'INTERNAL_ERROR');

        $puntjes = $this->puntjes();

        $this->expectException(ServerException::class);

        try {
            $puntjes->customers->register(new CreateCustomer(
                identifiers: [CreateIdentifier::card('CARD-NEW')],
            ));
        } finally {
            // Registering twice would create two customers, so one attempt only.
            self::assertSame(2, $this->fake->requestCount());
            self::assertSame([], $this->sleeps);
        }
    }

    public function test_verifying_a_redemption_is_never_retried(): void
    {
        $this->fake->queueError(500, 'INTERNAL_ERROR');

        $puntjes = $this->puntjes();

        $this->expectException(ServerException::class);

        try {
            // A replay would burn the code and answer CODE_ALREADY_USED.
            $puntjes->redemptions->verify('ABC-123');
        } finally {
            self::assertSame(2, $this->fake->requestCount());
        }
    }

    public function test_a_delete_is_retried_because_it_is_idempotent(): void
    {
        $this->fake->queueError(500, 'INTERNAL_ERROR');
        $this->fake->queueRaw(204, '');

        $this->puntjes()->products->delete('SKU-1');

        self::assertSame([0.5], $this->sleeps);
    }

    public function test_a_client_error_is_not_retried(): void
    {
        $this->fake->queueError(422, 'INSUFFICIENT_BALANCE', 'Insufficient balance.');

        $puntjes = $this->puntjes();

        try {
            $puntjes->wallets->adjust(9, new AdjustWallet(-500, 'Correction'));
            self::fail('Expected an ApiException.');
        } catch (ApiException $e) {
            self::assertSame('INSUFFICIENT_BALANCE', $e->code());
            self::assertSame([], $this->sleeps);
        }
    }

    public function test_retries_can_be_disabled(): void
    {
        $this->fake->queueError(500, 'INTERNAL_ERROR');

        $puntjes = $this->puntjes(maxRetries: 0);

        $this->expectException(ServerException::class);

        try {
            $puntjes->me();
        } finally {
            self::assertSame([], $this->sleeps);
        }
    }

    public function test_a_connection_failure_that_never_recovers_surfaces_as_a_transport_exception(): void
    {
        $this->fake->queueNetworkFailure();
        $this->fake->queueNetworkFailure();
        $this->fake->queueNetworkFailure();

        $this->expectException(TransportException::class);

        $this->puntjes(maxRetries: 2)->me();
    }

    public function test_a_rate_limit_exposes_retry_after_when_it_finally_gives_up(): void
    {
        $this->fake->queueError(429, 'RATE_LIMITED', 'Too many requests.', headers: ['Retry-After' => '30']);

        $puntjes = $this->puntjes(maxRetries: 0);

        try {
            $puntjes->me();
            self::fail('Expected a RateLimitException.');
        } catch (RateLimitException $e) {
            self::assertSame(30, $e->retryAfter());
        }
    }
}
