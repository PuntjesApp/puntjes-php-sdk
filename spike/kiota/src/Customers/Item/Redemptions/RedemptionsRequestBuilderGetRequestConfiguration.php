<?php

namespace Puntjes\Spike\Kiota\Customers\Item\Redemptions;

use Microsoft\Kiota\Abstractions\BaseRequestConfiguration;
use Microsoft\Kiota\Abstractions\RequestOption;

/**
 * Configuration for the request such as headers, query parameters, and middleware options.
*/
class RedemptionsRequestBuilderGetRequestConfiguration extends BaseRequestConfiguration 
{
    /**
     * @var RedemptionsRequestBuilderGetQueryParameters|null $queryParameters Request query parameters
    */
    public ?RedemptionsRequestBuilderGetQueryParameters $queryParameters = null;
    
    /**
     * Instantiates a new RedemptionsRequestBuilderGetRequestConfiguration and sets the default values.
     * @param array<string, array<string>|string>|null $headers Request headers
     * @param array<RequestOption>|null $options Request options
     * @param RedemptionsRequestBuilderGetQueryParameters|null $queryParameters Request query parameters
    */
    public function __construct(?array $headers = null, ?array $options = null, ?RedemptionsRequestBuilderGetQueryParameters $queryParameters = null) {
        parent::__construct($headers ?? [], $options ?? []);
        $this->queryParameters = $queryParameters;
    }

    /**
     * Instantiates a new RedemptionsRequestBuilderGetQueryParameters.
     * @param int|null $page 
     * @param GetStatusQueryParameterType|null $status 
     * @return RedemptionsRequestBuilderGetQueryParameters
    */
    public static function createQueryParameters(?int $page = null, ?GetStatusQueryParameterType $status = null): RedemptionsRequestBuilderGetQueryParameters {
        return new RedemptionsRequestBuilderGetQueryParameters($page, $status);
    }

}
