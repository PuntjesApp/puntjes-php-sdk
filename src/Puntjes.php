<?php

declare(strict_types=1);

namespace Puntjes;

use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Puntjes\Auth\ClientCredentialsProvider;
use Puntjes\Auth\InMemoryTokenStore;
use Puntjes\Auth\TokenProvider;
use Puntjes\Auth\TokenStore;
use Puntjes\Http\HttpClient;
use Puntjes\Http\Response;
use Puntjes\Http\Transport;
use Puntjes\Model\Campaign;
use Puntjes\Model\VendorBranding;
use Puntjes\Resource\Campaigns;
use Puntjes\Resource\Customers;
use Puntjes\Resource\Products;
use Puntjes\Resource\Redemptions;
use Puntjes\Resource\Rewards;
use Puntjes\Resource\Statistics;
use Puntjes\Resource\Transactions;
use Puntjes\Resource\Vouchers;
use Puntjes\Resource\Wallets;

/**
 * The Puntjes API client.
 *
 *     $puntjes = Puntjes::make(
 *         clientId: getenv('PUNTJES_CLIENT_ID'),
 *         clientSecret: getenv('PUNTJES_CLIENT_SECRET'),
 *         baseUrl:      'https://puntjes.app/api/v1',
 *     );
 *
 *     $customer = $puntjes->customers->lookup(identifier: $scannedCard);
 *
 *     $puntjes->transactions->submit(new SubmitTransaction(
 *         identifier: $scannedCard,
 *         totalAmount: 4200,              // cents
 *         idempotencyKey: 'order-'.$order->id,
 *     ));
 *
 * Authentication is handled for you: a `client_credentials` token is fetched on the
 * first call, cached until it expires, and re-fetched once if the API ever rejects
 * it. Supply a shared {@see TokenStore} in a web application so the token outlives
 * a single request.
 *
 * All monetary amounts across this API are in CENTS, with one exception documented
 * on {@see Campaign::$minTransactionAmount}.
 */
final class Puntjes
{
    public readonly Customers $customers;

    public readonly Transactions $transactions;

    public readonly Wallets $wallets;

    public readonly Rewards $rewards;

    public readonly Redemptions $redemptions;

    public readonly Products $products;

    public readonly Campaigns $campaigns;

    public readonly Statistics $statistics;

    public readonly Vouchers $vouchers;

    public function __construct(
        private readonly Config $config,
        private readonly Transport $transport,
        private readonly HttpClient $http,
    ) {
        $this->customers = new Customers($transport);
        $this->transactions = new Transactions($transport);
        $this->wallets = new Wallets($transport);
        $this->rewards = new Rewards($transport);
        $this->redemptions = new Redemptions($transport);
        $this->products = new Products($transport);
        $this->campaigns = new Campaigns($transport);
        $this->statistics = new Statistics($transport);
        $this->vouchers = new Vouchers($transport);
    }

    /**
     * Build a client from credentials, wiring up sensible defaults.
     *
     * @param  string  $baseUrl  The base URL from the API docs, `https://puntjes.app/api/v1`.
     *                           The bare host works too — see {@see Config}.
     * @param  TokenStore|null  $tokenStore  Defaults to per-process caching. Pass a shared
     *                                       store (Laravel cache, WP transients) in a web app.
     * @param  ClientInterface|null  $httpClient  Any PSR-18 client. Auto-discovered when omitted;
     *                                            pass one to control timeouts, proxies and TLS.
     * @param  array<string, string>  $defaultHeaders
     */
    public static function make(
        string $clientId,
        string $clientSecret,
        string $baseUrl,
        ?TokenStore $tokenStore = null,
        ?ClientInterface $httpClient = null,
        ?RequestFactoryInterface $requestFactory = null,
        ?StreamFactoryInterface $streamFactory = null,
        int $maxRetries = 2,
        array $defaultHeaders = [],
    ): self {
        return self::fromConfig(
            new Config(
                clientId: $clientId,
                clientSecret: $clientSecret,
                baseUrl: $baseUrl,
                maxRetries: $maxRetries,
                defaultHeaders: $defaultHeaders,
            ),
            $tokenStore,
            new HttpClient($httpClient, $requestFactory, $streamFactory),
        );
    }

    /** Build from a prepared {@see Config} — the entry point framework packages use. */
    public static function fromConfig(
        Config $config,
        ?TokenStore $tokenStore = null,
        ?HttpClient $http = null,
        ?TokenProvider $tokenProvider = null,
    ): self {
        $http ??= new HttpClient;

        $tokenProvider ??= new ClientCredentialsProvider(
            $config,
            $http,
            $tokenStore ?? new InMemoryTokenStore,
        );

        return new self($config, new Transport($config, $http, $tokenProvider), $http);
    }

    /**
     * The authenticated vendor's branding.
     *
     * The cheapest way to prove a set of credentials works end to end — it exercises
     * the token grant, the vendor resolution and the active-account check.
     */
    public function me(): VendorBranding
    {
        return VendorBranding::fromArray($this->transport->get('/me')->dataArray());
    }

    /**
     * Liveness check against `GET /health`. Returns true when the API answers `ok`.
     *
     * Unauthenticated on purpose: it bypasses the transport entirely, so it stays
     * useful for diagnosing whether a problem is reachability or credentials.
     */
    public function ping(): bool
    {
        $response = $this->http->send('GET', $this->config->apiUrl('/health'), headers: ['Accept' => 'application/json']);

        if (! $response->isSuccessful()) {
            return false;
        }

        $data = $response->tryJson()['data'] ?? null;

        return is_array($data) && ($data['status'] ?? null) === 'ok';
    }

    /**
     * Escape hatch for endpoints this SDK does not model yet, or for reading fields
     * added to the API after this version shipped.
     *
     * Returns the raw {@see Response}, with authentication, retries and error mapping
     * still applied.
     *
     * @param  array<string, mixed>  $query
     * @param  array<string, mixed>|null  $body
     */
    public function request(string $method, string $path, array $query = [], ?array $body = null): Response
    {
        return $this->transport->request($method, $path, $query, $body);
    }

    public function config(): Config
    {
        return $this->config;
    }
}
