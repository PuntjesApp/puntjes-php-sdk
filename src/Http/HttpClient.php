<?php

declare(strict_types=1);

namespace Puntjes\Http;

use Http\Discovery\Psr17FactoryDiscovery;
use Http\Discovery\Psr18ClientDiscovery;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Puntjes\Exception\TransportException;

/**
 * Builds and sends one HTTP request using whatever PSR-18 client the host app has.
 *
 * The SDK never requires Guzzle. That is not a stylistic preference: a WordPress
 * plugin runs in a process shared with every other plugin, and bundling a second
 * copy of a common library is the classic way to break a site. Anything PSR-18
 * works, and `php-http/discovery` finds it automatically.
 *
 * Timeouts, proxies and TLS options belong to that client — configure them there
 * and pass the configured instance in.
 */
final class HttpClient
{
    private ClientInterface $client;

    private RequestFactoryInterface $requestFactory;

    private StreamFactoryInterface $streamFactory;

    public function __construct(
        ?ClientInterface $client = null,
        ?RequestFactoryInterface $requestFactory = null,
        ?StreamFactoryInterface $streamFactory = null,
    ) {
        $this->client = $client ?? Psr18ClientDiscovery::find();
        $this->requestFactory = $requestFactory ?? Psr17FactoryDiscovery::findRequestFactory();
        $this->streamFactory = $streamFactory ?? Psr17FactoryDiscovery::findStreamFactory();
    }

    /**
     * @param  array<string, mixed>  $query
     * @param  array<string, string>  $headers
     * @param  string|null  $body  Pre-encoded request body.
     *
     * @throws TransportException when the request never reached a response.
     */
    public function send(
        string $method,
        string $url,
        array $query = [],
        array $headers = [],
        ?string $body = null,
    ): Response {
        $request = $this->requestFactory->createRequest($method, self::appendQuery($url, $query));

        foreach ($headers as $name => $value) {
            $request = $request->withHeader($name, $value);
        }

        if ($body !== null) {
            $request = $request->withBody($this->streamFactory->createStream($body));
        }

        try {
            $response = $this->client->sendRequest($request);
        } catch (ClientExceptionInterface $e) {
            throw new TransportException(
                sprintf('Could not reach the Puntjes API (%s %s): %s', $method, $url, $e->getMessage()),
                0,
                $e,
            );
        }

        $headers = [];

        foreach ($response->getHeaders() as $name => $values) {
            $headers[strtolower($name)] = implode(', ', $values);
        }

        return new Response(
            status: $response->getStatusCode(),
            headers: $headers,
            body: (string) $response->getBody(),
        );
    }

    /**
     * Append query parameters to a URL, dropping nulls.
     *
     * Booleans are encoded as `1`/`0` rather than PHP's default `1`/empty string,
     * because the API reads them through `Request::boolean()`, which treats an empty
     * value as false but an absent-vs-empty distinction as meaningful elsewhere.
     *
     * @param  array<string, mixed>  $query
     */
    private static function appendQuery(string $url, array $query): string
    {
        $filtered = [];

        foreach ($query as $key => $value) {
            if ($value === null) {
                continue;
            }

            $filtered[$key] = is_bool($value) ? ($value ? '1' : '0') : $value;
        }

        if ($filtered === []) {
            return $url;
        }

        $separator = str_contains($url, '?') ? '&' : '?';

        return $url.$separator.http_build_query($filtered, '', '&', PHP_QUERY_RFC3986);
    }
}
