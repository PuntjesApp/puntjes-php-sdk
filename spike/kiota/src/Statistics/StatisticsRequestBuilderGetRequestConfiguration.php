<?php

namespace Puntjes\Spike\Kiota\Statistics;

use Microsoft\Kiota\Abstractions\BaseRequestConfiguration;
use Microsoft\Kiota\Abstractions\RequestOption;

/**
 * Configuration for the request such as headers, query parameters, and middleware options.
*/
class StatisticsRequestBuilderGetRequestConfiguration extends BaseRequestConfiguration 
{
    /**
     * @var StatisticsRequestBuilderGetQueryParameters|null $queryParameters Request query parameters
    */
    public ?StatisticsRequestBuilderGetQueryParameters $queryParameters = null;
    
    /**
     * Instantiates a new StatisticsRequestBuilderGetRequestConfiguration and sets the default values.
     * @param array<string, array<string>|string>|null $headers Request headers
     * @param array<RequestOption>|null $options Request options
     * @param StatisticsRequestBuilderGetQueryParameters|null $queryParameters Request query parameters
    */
    public function __construct(?array $headers = null, ?array $options = null, ?StatisticsRequestBuilderGetQueryParameters $queryParameters = null) {
        parent::__construct($headers ?? [], $options ?? []);
        $this->queryParameters = $queryParameters;
    }

    /**
     * Instantiates a new StatisticsRequestBuilderGetQueryParameters.
     * @param string|null $branch 
     * @param GetPeriodQueryParameterType|null $period 
     * @param int|null $top_products_limit 
     * @return StatisticsRequestBuilderGetQueryParameters
    */
    public static function createQueryParameters(?string $branch = null, ?GetPeriodQueryParameterType $period = null, ?int $top_products_limit = null): StatisticsRequestBuilderGetQueryParameters {
        return new StatisticsRequestBuilderGetQueryParameters($branch, $period, $top_products_limit);
    }

}
