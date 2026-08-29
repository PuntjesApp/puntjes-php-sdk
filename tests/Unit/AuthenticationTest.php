<?php

declare(strict_types=1);

namespace Puntjes\Tests\Unit;

use Nyholm\Psr7\Factory\Psr17Factory;
use Puntjes\Auth\AccessToken;
use Puntjes\Auth\ClientCredentialsProvider;
use Puntjes\Auth\InMemoryTokenStore;
use Puntjes\Config;
use Puntjes\Exception\AuthenticationException;
use Puntjes\Exception\ServerException;
use Puntjes\Http\HttpClient;
use Puntjes\Request\CreateCustomer;
use Puntjes\Request\CreateIdentifier;
use Puntjes\Tests\Support\TestCase;

final class AuthenticationTest extends TestCase
{
    public function test_it_grants_a_token_before_the_first_call(): void
    {
        $this->fake->queueToken('tok-abc');
        $this->fake->queueData(['display_name' => 'Bakkerij Jan']);

        $puntjes = $this->puntjes();
        $puntjes->me();

        self::assertSame(2, $this->fake->requestCount());
        self::assertSame('https://app.puntjes.test/oauth/token', $this->fake->uriAt(0));
        self::assertSame('https://app.puntjes.test/api/v1/me', $this->fake->uriAt(1));
        self::assertSame('Bearer tok-abc', $this->fake->requestAt(1)->getHeaderLine('Authorization'));
    }

    public function test_it_sends_the_grant_as_form_encoded_per_rfc_6749(): void
    {
        $this->fake->queueToken();
        $this->fake->queueData(['display_name' => 'Bakkerij Jan']);

        $this->puntjes()->me();

        $grant = $this->fake->requestAt(0);
        self::assertSame('application/x-www-form-urlencoded', $grant->getHeaderLine('Content-Type'));

        parse_str($this->fake->bodies[0], $fields);
        self::assertSame([
            'grant_type' => 'client_credentials',
            'client_id' => 'test-client',
            'client_secret' => 'test-secret',
        ], $fields);
    }

    public function test_it_reuses_a_cached_token_across_calls(): void
    {
        $this->fake->queueToken();
        $this->fake->queueData(['display_name' => 'One']);
        $this->fake->queueData(['display_name' => 'Two']);

        $puntjes = $this->puntjes();
        $puntjes->me();
        $puntjes->me();

        // Three requests, not four: the token was granted once.
        self::assertSame(3, $this->fake->requestCount());
    }

    public function test_a_shared_store_survives_client_instances(): void
    {
        $store = new InMemoryTokenStore;

        $this->fake->queueToken('tok-shared');
        $this->fake->queueData(['display_name' => 'One']);
        $this->puntjes(store: $store)->me();

        // A second client with the same credentials and store must not re-grant —
        // this is what makes the Laravel/WordPress stores worth having.
        $this->fake->queueData(['display_name' => 'Two']);
        $this->puntjes(store: $store)->me();

        self::assertSame(3, $this->fake->requestCount());
        self::assertSame('Bearer tok-shared', $this->fake->requestAt(2)->getHeaderLine('Authorization'));
    }

    public function test_an_expired_cached_token_is_replaced(): void
    {
        $store = new InMemoryTokenStore;
        $config = $this->config();
        $psr17 = new Psr17Factory;
        $http = new HttpClient($this->fake, $psr17, $psr17);
        $provider = new ClientCredentialsProvider($config, $http, $store);

        // Still 5 seconds of nominal life left, but inside the 30s leeway — a request
        // sent with it could easily be rejected mid-flight, so it must be replaced.
        $store->put(
            'puntjes:token:'.$config->credentialFingerprint(),
            new AccessToken('stale', time() + 5),
            5,
        );

        $this->fake->queueToken('tok-fresh');

        self::assertSame('tok-fresh', $provider->token()->accessToken);
    }

    public function test_a_401_triggers_exactly_one_silent_regrant_and_replay(): void
    {
        $this->fake->queueToken('tok-old');
        $this->fake->queueError(401, 'UNAUTHENTICATED', 'Authentication required.');
        $this->fake->queueToken('tok-new');
        $this->fake->queueData(['display_name' => 'Bakkerij Jan']);

        $branding = $this->puntjes()->me();

        self::assertSame('Bakkerij Jan', $branding->displayName);
        self::assertSame(4, $this->fake->requestCount());
        self::assertSame('Bearer tok-old', $this->fake->requestAt(1)->getHeaderLine('Authorization'));
        self::assertSame('Bearer tok-new', $this->fake->requestAt(3)->getHeaderLine('Authorization'));
    }

    public function test_a_second_401_surfaces_rather_than_looping(): void
    {
        $this->fake->queueToken('tok-old');
        $this->fake->queueError(401, 'UNAUTHENTICATED');
        $this->fake->queueToken('tok-new');
        $this->fake->queueError(401, 'UNAUTHENTICATED');

        $this->expectException(AuthenticationException::class);

        $this->puntjes()->me();
    }

    public function test_rejected_credentials_raise_an_authentication_exception_without_leaking_the_secret(): void
    {
        $this->fake->queueTokenFailure(401, [
            'error' => 'invalid_client',
            'error_description' => 'Client authentication failed',
        ]);

        try {
            $this->puntjes()->me();
            self::fail('Expected an AuthenticationException.');
        } catch (AuthenticationException $e) {
            self::assertSame('INVALID_CLIENT', $e->code());
            self::assertStringContainsString('Client authentication failed', $e->getMessage());
            self::assertStringNotContainsString('test-secret', $e->getMessage());
        }
    }

    public function test_tokens_are_cached_per_credential_pair(): void
    {
        $a = $this->config();
        $b = new Config('other-client', 'other-secret', 'https://app.puntjes.test');

        self::assertNotSame($a->credentialFingerprint(), $b->credentialFingerprint());
    }

    public function test_the_health_check_needs_no_token(): void
    {
        $this->fake->queueData(['status' => 'ok']);

        self::assertTrue($this->puntjes()->ping());
        self::assertSame(1, $this->fake->requestCount());
        self::assertFalse($this->fake->requestAt(0)->hasHeader('Authorization'));
    }

    public function test_a_transient_grant_failure_is_retried_under_the_normal_policy(): void
    {
        $this->fake->queueTokenFailure(500, [
            'error' => ['code' => 'INTERNAL_ERROR', 'message' => 'An unexpected error occurred.', 'status' => 500, 'request_id' => 'r1'],
        ]);
        $this->fake->queueToken('tok-after-blip');
        $this->fake->queueData(['display_name' => 'Bakkerij Jan']);

        $branding = $this->puntjes()->me();

        self::assertSame('Bakkerij Jan', $branding->displayName);
        self::assertSame([0.5], $this->sleeps);
        self::assertSame('Bearer tok-after-blip', $this->fake->requestAt(2)->getHeaderLine('Authorization'));
    }

    public function test_a_connection_failure_during_the_grant_is_retried(): void
    {
        $this->fake->queueTokenNetworkFailure();
        $this->fake->queueData(['display_name' => 'Bakkerij Jan']);

        self::assertSame('Bakkerij Jan', $this->puntjes()->me()->displayName);
        self::assertSame([0.5], $this->sleeps);
    }

    public function test_a_rate_limited_grant_honours_retry_after(): void
    {
        $this->fake->queueTokenFailure(
            429,
            ['error' => ['code' => 'RATE_LIMITED', 'message' => 'Too many requests.', 'status' => 429, 'request_id' => 'r1']],
            ['Retry-After' => '3'],
        );
        $this->fake->queueData(['display_name' => 'Bakkerij Jan']);

        $this->puntjes()->me();

        self::assertSame([3.0], $this->sleeps);
    }

    public function test_grant_failures_are_retried_even_for_posts_without_idempotency_keys(): void
    {
        // The grant failing means POST /customers was never sent, so replaying it
        // cannot create a duplicate — the request's own no-retry rule must not apply.
        $this->fake->queueTokenFailure(500, [
            'error' => ['code' => 'INTERNAL_ERROR', 'message' => 'An unexpected error occurred.', 'status' => 500, 'request_id' => 'r1'],
        ]);
        $this->fake->queueData(['id' => 7], 201);

        $customer = $this->puntjes()->customers->register(new CreateCustomer(
            identifiers: [CreateIdentifier::loyaltyCard('B3H9RT5W')],
        ));

        self::assertSame(7, $customer->id);
        self::assertSame([0.5], $this->sleeps);
        // Exactly one actual API request — the retry replayed the grant, not the POST.
        self::assertSame(1, $this->fake->apiRequestCount());
    }

    public function test_a_grant_5xx_is_a_server_error_not_an_authentication_failure(): void
    {
        // Typing an outage as an auth failure sends an operator hunting for a
        // credentials bug during an incident.
        $this->fake->queueTokenFailure(500, [
            'error' => ['code' => 'INTERNAL_ERROR', 'message' => 'An unexpected error occurred.', 'status' => 500, 'request_id' => 'r1'],
        ]);

        $puntjes = $this->puntjes(maxRetries: 0);

        try {
            $puntjes->me();
            self::fail('Expected a ServerException.');
        } catch (ServerException $e) {
            self::assertSame('INTERNAL_ERROR', $e->code());
            self::assertSame(500, $e->status());
        }
    }

    public function test_rejected_credentials_are_never_retried(): void
    {
        $this->fake->queueTokenFailure(401, [
            'error' => 'invalid_client',
            'error_description' => 'Client authentication failed',
        ]);

        $puntjes = $this->puntjes();

        $this->expectException(AuthenticationException::class);

        try {
            $puntjes->me();
        } finally {
            // One grant attempt, no sleeps: bad credentials are not transient.
            self::assertSame(1, $this->fake->requestCount());
            self::assertSame([], $this->sleeps);
        }
    }
}
