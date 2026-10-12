<?php

declare(strict_types=1);

namespace Puntjes\Tests\Contract\Support;

use Http\Discovery\Psr18ClientDiscovery;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Sends through the discovered PSR-18 client and checks every JSON answer for its required fields.
 */
final class CheckedHttpClient implements ClientInterface
{
    /** @var list<string> */
    private static array $gaps = [];

    private ClientInterface $inner;

    public function __construct(private readonly RequiredFields $fields, private readonly string $apiPath)
    {
        $this->inner = Psr18ClientDiscovery::find();
    }

    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        $response = $this->inner->sendRequest($request);
        $path = $request->getUri()->getPath();

        if (str_starts_with($path, $this->apiPath.'/') && str_contains($response->getHeaderLine('Content-Type'), 'json')) {
            $body = json_decode((string) $response->getBody(), true);
            $response->getBody()->rewind();

            array_push(self::$gaps, ...$this->fields->missing($request->getMethod(), substr($path, strlen($this->apiPath)), $response->getStatusCode(), $body));
        }

        return $response;
    }

    /** @return list<string> */
    public static function gaps(): array
    {
        return array_values(array_unique(self::$gaps));
    }
}
