<?php

declare(strict_types=1);

namespace Puntjes\Tests\Support;

use PHPUnit\Framework\TestCase as BaseTestCase;
use Puntjes\Auth\TokenStore;
use Puntjes\Config;
use Puntjes\Generated\PuntjesClient;
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

    /** The 2.0 client from {@see Puntjes::make()}, wired to {@see $fake}. Token grants are served automatically. */
    protected function puntjes(int $maxRetries = 2, ?TokenStore $store = null): PuntjesClient
    {
        return Puntjes::make(
            'test-client',
            'test-secret',
            'https://app.puntjes.test',
            tokenStore: $store,
            httpClient: $this->fake,
            maxRetries: $maxRetries,
            sleeper: function (float $seconds): void {
                $this->sleeps[] = $seconds;
            },
        );
    }
}
