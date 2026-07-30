<?php

declare(strict_types=1);

namespace Puntjes\Tests\Support;

use Nyholm\Psr7\Factory\Psr17Factory;
use PHPUnit\Framework\TestCase as BaseTestCase;
use Puntjes\Auth\ClientCredentialsProvider;
use Puntjes\Auth\InMemoryTokenStore;
use Puntjes\Auth\TokenStore;
use Puntjes\Config;
use Puntjes\Http\HttpClient;
use Puntjes\Http\Transport;
use Puntjes\Puntjes;

abstract class TestCase extends BaseTestCase
{
    protected FakeHttpClient $fake;

    /**
     * Seconds each retry would have slept, so backoff is asserted without waiting.
     *
     * @var array<int, float>
     */
    protected array $sleeps = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->fake = new FakeHttpClient;
        $this->sleeps = [];
    }

    protected function config(int $maxRetries = 2): Config
    {
        return new Config(
            clientId: 'test-client',
            clientSecret: 'test-secret',
            baseUrl: 'https://app.puntjes.test',
            maxRetries: $maxRetries,
            retryBaseDelay: 0.5,
        );
    }

    /** A client wired to {@see $fake}. Token grants are served automatically. */
    protected function puntjes(int $maxRetries = 2, ?TokenStore $store = null): Puntjes
    {
        $config = $this->config($maxRetries);
        $psr17 = new Psr17Factory;
        $http = new HttpClient($this->fake, $psr17, $psr17);

        $provider = new ClientCredentialsProvider($config, $http, $store ?? new InMemoryTokenStore);

        $transport = new Transport(
            $config,
            $http,
            $provider,
            function (float $seconds): void {
                $this->sleeps[] = $seconds;
            },
        );

        return new Puntjes($config, $transport, $http);
    }
}
