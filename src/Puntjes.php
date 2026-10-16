<?php

declare(strict_types=1);

namespace Puntjes;

use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Puntjes\Auth\ClientCredentialsProvider;
use Puntjes\Auth\InMemoryTokenStore;
use Puntjes\Auth\TokenStore;
use Puntjes\Generated\PuntjesClient;
use Puntjes\Http\HttpClient;
use Puntjes\Http\Psr18RequestAdapter;
use Puntjes\Http\Transport;

/**
 * Builds the generated client on the project's own PSR-18 client, with the token, retry and error rules of Transport.
 */
final class Puntjes
{
    private function __construct() {}

    /**
     * @param  array<string, string>  $defaultHeaders
     * @param  (callable(float): void)|null  $sleeper
     */
    public static function make(
        string $clientId,
        string $clientSecret,
        string $baseUrl = 'https://puntjes.app',
        ?TokenStore $tokenStore = null,
        ?ClientInterface $httpClient = null,
        ?RequestFactoryInterface $requestFactory = null,
        ?StreamFactoryInterface $streamFactory = null,
        int $maxRetries = 2,
        array $defaultHeaders = [],
        ?callable $sleeper = null,
    ): PuntjesClient {
        $config = new Config($clientId, $clientSecret, $baseUrl, $maxRetries, defaultHeaders: $defaultHeaders);
        $http = new HttpClient($httpClient, $requestFactory, $streamFactory);
        $tokens = new ClientCredentialsProvider($config, $http, $tokenStore ?? new InMemoryTokenStore);

        return new PuntjesClient(new Psr18RequestAdapter(new Transport($config, $http, $tokens, $sleeper), rtrim($config->apiUrl(''), '/')));
    }
}
