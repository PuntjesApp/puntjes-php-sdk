<?php

namespace Puntjes\Spike\Kiota\Rewards;

use Microsoft\Kiota\Abstractions\BaseRequestConfiguration;
use Microsoft\Kiota\Abstractions\RequestOption;

/**
 * Configuration for the request such as headers, query parameters, and middleware options.
*/
class RewardsRequestBuilderGetRequestConfiguration extends BaseRequestConfiguration 
{
    /**
     * @var RewardsRequestBuilderGetQueryParameters|null $queryParameters Request query parameters
    */
    public ?RewardsRequestBuilderGetQueryParameters $queryParameters = null;
    
    /**
     * Instantiates a new RewardsRequestBuilderGetRequestConfiguration and sets the default values.
     * @param array<string, array<string>|string>|null $headers Request headers
     * @param array<RequestOption>|null $options Request options
     * @param RewardsRequestBuilderGetQueryParameters|null $queryParameters Request query parameters
    */
    public function __construct(?array $headers = null, ?array $options = null, ?RewardsRequestBuilderGetQueryParameters $queryParameters = null) {
        parent::__construct($headers ?? [], $options ?? []);
        $this->queryParameters = $queryParameters;
    }

    /**
     * Instantiates a new RewardsRequestBuilderGetQueryParameters.
     * @param bool|null $affordable 
     * @param string|null $identifier 
     * @return RewardsRequestBuilderGetQueryParameters
    */
    public static function createQueryParameters(?bool $affordable = null, ?string $identifier = null): RewardsRequestBuilderGetQueryParameters {
        return new RewardsRequestBuilderGetQueryParameters($affordable, $identifier);
    }

}
