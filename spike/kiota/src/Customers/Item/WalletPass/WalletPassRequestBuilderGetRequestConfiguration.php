<?php

namespace Puntjes\Spike\Kiota\Customers\Item\WalletPass;

use Microsoft\Kiota\Abstractions\BaseRequestConfiguration;
use Microsoft\Kiota\Abstractions\RequestOption;

/**
 * Configuration for the request such as headers, query parameters, and middleware options.
*/
class WalletPassRequestBuilderGetRequestConfiguration extends BaseRequestConfiguration 
{
    /**
     * @var WalletPassRequestBuilderGetQueryParameters|null $queryParameters Request query parameters
    */
    public ?WalletPassRequestBuilderGetQueryParameters $queryParameters = null;
    
    /**
     * Instantiates a new WalletPassRequestBuilderGetRequestConfiguration and sets the default values.
     * @param array<string, array<string>|string>|null $headers Request headers
     * @param array<RequestOption>|null $options Request options
     * @param WalletPassRequestBuilderGetQueryParameters|null $queryParameters Request query parameters
    */
    public function __construct(?array $headers = null, ?array $options = null, ?WalletPassRequestBuilderGetQueryParameters $queryParameters = null) {
        parent::__construct($headers ?? [], $options ?? []);
        $this->queryParameters = $queryParameters;
    }

    /**
     * Instantiates a new WalletPassRequestBuilderGetQueryParameters.
     * @param GetPlatformQueryParameterType|null $platform 
     * @return WalletPassRequestBuilderGetQueryParameters
    */
    public static function createQueryParameters(?GetPlatformQueryParameterType $platform = null): WalletPassRequestBuilderGetQueryParameters {
        return new WalletPassRequestBuilderGetQueryParameters($platform);
    }

}
