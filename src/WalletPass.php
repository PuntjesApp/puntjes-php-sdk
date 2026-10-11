<?php

declare(strict_types=1);

namespace Puntjes;

use Microsoft\Kiota\Abstractions\RequestAdapter;
use Psr\Http\Message\StreamInterface;
use Puntjes\Exception\TransportException;
use Puntjes\Generated\Customers\Item\WalletPass\GetPlatformQueryParameterType;
use Puntjes\Generated\Customers\Item\WalletPass\WalletPassRequestBuilderGetRequestConfiguration;
use Puntjes\Generated\PuntjesClient;
use ReflectionProperty;

/**
 * The Apple wallet pass, a pkpass file: the spec gives its 200 two media types, and Kiota reads only the JSON one.
 */
final class WalletPass
{
    public static function apple(PuntjesClient $client, int|string $customer): StreamInterface
    {
        $config = new WalletPassRequestBuilderGetRequestConfiguration(
            queryParameters: WalletPassRequestBuilderGetRequestConfiguration::createQueryParameters(new GetPlatformQueryParameterType('apple')),
        );
        $request = $client->customers()->byCustomer((string) $customer)->walletPass()->toGetRequestInformation($config);
        $request->removeHeader('Accept');
        $request->addHeader('Accept', 'application/vnd.apple.pkpass');

        /** @var RequestAdapter $adapter */
        $adapter = (new ReflectionProperty($client, 'requestAdapter'))->getValue($client);

        $pass = $adapter->sendPrimitiveAsync($request, StreamInterface::class)->wait();
        if (! $pass instanceof StreamInterface) {
            throw new TransportException('The Puntjes API answered the Apple wallet pass request with an empty body.');
        }

        return $pass;
    }
}
