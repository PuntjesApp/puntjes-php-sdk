<?php

namespace Puntjes\Spike\Kiota\Customers\Lookup;

use Microsoft\Kiota\Abstractions\BaseRequestConfiguration;
use Microsoft\Kiota\Abstractions\RequestOption;

/**
 * Configuration for the request such as headers, query parameters, and middleware options.
*/
class LookupRequestBuilderGetRequestConfiguration extends BaseRequestConfiguration 
{
    /**
     * @var LookupRequestBuilderGetQueryParameters|null $queryParameters Request query parameters
    */
    public ?LookupRequestBuilderGetQueryParameters $queryParameters = null;
    
    /**
     * Instantiates a new LookupRequestBuilderGetRequestConfiguration and sets the default values.
     * @param array<string, array<string>|string>|null $headers Request headers
     * @param array<RequestOption>|null $options Request options
     * @param LookupRequestBuilderGetQueryParameters|null $queryParameters Request query parameters
    */
    public function __construct(?array $headers = null, ?array $options = null, ?LookupRequestBuilderGetQueryParameters $queryParameters = null) {
        parent::__construct($headers ?? [], $options ?? []);
        $this->queryParameters = $queryParameters;
    }

    /**
     * Instantiates a new LookupRequestBuilderGetQueryParameters.
     * @param string|null $external_id 
     * @param string|null $identifier 
     * @return LookupRequestBuilderGetQueryParameters
    */
    public static function createQueryParameters(?string $external_id = null, ?string $identifier = null): LookupRequestBuilderGetQueryParameters {
        return new LookupRequestBuilderGetQueryParameters($external_id, $identifier);
    }

}
