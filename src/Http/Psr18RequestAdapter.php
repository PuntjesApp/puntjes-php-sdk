<?php

declare(strict_types=1);

namespace Puntjes\Http;

use Exception;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Utils;
use Http\Promise\FulfilledPromise;
use Http\Promise\Promise;
use Http\Promise\RejectedPromise;
use Microsoft\Kiota\Abstractions\RequestAdapter;
use Microsoft\Kiota\Abstractions\RequestInformation;
use Microsoft\Kiota\Abstractions\Serialization\ParseNode;
use Microsoft\Kiota\Abstractions\Serialization\ParseNodeFactory;
use Microsoft\Kiota\Abstractions\Serialization\ParseNodeFactoryRegistry;
use Microsoft\Kiota\Abstractions\Serialization\SerializationWriterFactory;
use Microsoft\Kiota\Abstractions\Serialization\SerializationWriterFactoryRegistry;
use Microsoft\Kiota\Abstractions\Store\BackingStoreFactory;
use Psr\Http\Message\StreamInterface;
use Puntjes\Exception\ConfigurationException;

/**
 * Kiota's RequestAdapter on Transport: the generated client builds each request, Transport sends it with the
 * token, the retry rule and the typed exceptions. Kiota's own errorMappings are not used.
 * A URL outside the base URL is refused, so the bearer token never leaves for another host.
 */
final class Psr18RequestAdapter implements RequestAdapter
{
    public function __construct(private readonly Transport $transport, private string $baseUrl) {}

    public function sendAsync(RequestInformation $requestInfo, array $targetCallable, ?array $errorMappings = null): Promise
    {
        return $this->attempt(fn () => $this->root($this->send($requestInfo))?->getObjectValue($targetCallable));
    }

    public function sendCollectionAsync(RequestInformation $requestInfo, array $targetCallable, ?array $errorMappings = null): Promise
    {
        return $this->attempt(fn () => $this->root($this->send($requestInfo))?->getCollectionOfObjectValues($targetCallable));
    }

    public function sendPrimitiveAsync(RequestInformation $requestInfo, string $primitiveType, ?array $errorMappings = null): Promise
    {
        return $this->attempt(function () use ($requestInfo, $primitiveType) {
            $response = $this->send($requestInfo);
            if ($primitiveType === StreamInterface::class) {
                return $response->body === '' ? null : Utils::streamFor($response->body);
            }
            $node = $this->root($response);

            return match ($primitiveType) {
                'int', 'long' => $node?->getIntegerValue(),
                'float' => $node?->getFloatValue(),
                'bool' => $node?->getBooleanValue(),
                default => $node?->getStringValue(),
            };
        });
    }

    public function sendPrimitiveCollectionAsync(RequestInformation $requestInfo, string $primitiveType, ?array $errorMappings = null): Promise
    {
        return $this->attempt(fn () => $this->root($this->send($requestInfo))?->getCollectionOfPrimitiveValues($primitiveType));
    }

    public function sendNoContentAsync(RequestInformation $requestInfo, ?array $errorMappings = null): Promise
    {
        return $this->attempt(function () use ($requestInfo) {
            $this->send($requestInfo);

            return null;
        });
    }

    public function getSerializationWriterFactory(): SerializationWriterFactory
    {
        return SerializationWriterFactoryRegistry::getDefaultInstance();
    }

    public function getParseNodeFactory(): ParseNodeFactory
    {
        return ParseNodeFactoryRegistry::getDefaultInstance();
    }

    public function enableBackingStore(BackingStoreFactory $backingStoreFactory): void {}

    public function setBaseUrl(string $baseUrl): void
    {
        $this->baseUrl = $baseUrl;
    }

    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    public function convertToNative(RequestInformation $requestInformation): Promise
    {
        return $this->attempt(fn () => new Request(
            $requestInformation->httpMethod,
            $this->uri($requestInformation),
            $requestInformation->getHeaders()->getAll(),
            $requestInformation->content,
        ));
    }

    private function send(RequestInformation $info): Response
    {
        $headers = [];
        foreach ($info->getHeaders()->getAll() as $name => $values) {
            $headers[$name] = implode(', ', $values);
        }
        $body = $info->content === null ? null : (string) $info->content;

        return $this->transport->sendRaw($info->httpMethod, $this->uri($info), $headers, $body);
    }

    private function uri(RequestInformation $info): string
    {
        $info->pathParameters['baseurl'] = $this->baseUrl;
        $uri = $info->getUri();
        if (! str_starts_with($uri, $this->baseUrl.'/') && $uri !== $this->baseUrl) {
            throw new ConfigurationException(sprintf('Refusing to send the API token to %s: it is outside %s.', $uri, $this->baseUrl));
        }

        return $uri;
    }

    private function root(Response $response): ?ParseNode
    {
        $type = $response->header('content-type');
        if ($response->body === '' || $type === null) {
            return null;
        }

        return $this->getParseNodeFactory()->getRootParseNode(explode(';', $type)[0], Utils::streamFor($response->body));
    }

    /**
     * @param  callable(): mixed  $work
     * @return Promise<mixed>
     */
    private function attempt(callable $work): Promise
    {
        try {
            return new FulfilledPromise($work());
        } catch (Exception $e) {
            return new RejectedPromise($e);
        }
    }
}
