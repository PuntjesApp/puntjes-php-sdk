<?php

namespace Puntjes\Spike\Kiota\Campaigns;

use Microsoft\Kiota\Abstractions\BaseRequestConfiguration;
use Microsoft\Kiota\Abstractions\RequestOption;

/**
 * Configuration for the request such as headers, query parameters, and middleware options.
*/
class CampaignsRequestBuilderGetRequestConfiguration extends BaseRequestConfiguration 
{
    /**
     * @var CampaignsRequestBuilderGetQueryParameters|null $queryParameters Request query parameters
    */
    public ?CampaignsRequestBuilderGetQueryParameters $queryParameters = null;
    
    /**
     * Instantiates a new CampaignsRequestBuilderGetRequestConfiguration and sets the default values.
     * @param array<string, array<string>|string>|null $headers Request headers
     * @param array<RequestOption>|null $options Request options
     * @param CampaignsRequestBuilderGetQueryParameters|null $queryParameters Request query parameters
    */
    public function __construct(?array $headers = null, ?array $options = null, ?CampaignsRequestBuilderGetQueryParameters $queryParameters = null) {
        parent::__construct($headers ?? [], $options ?? []);
        $this->queryParameters = $queryParameters;
    }

    /**
     * Instantiates a new CampaignsRequestBuilderGetQueryParameters.
     * @param string|null $branch 
     * @param int|null $page 
     * @return CampaignsRequestBuilderGetQueryParameters
    */
    public static function createQueryParameters(?string $branch = null, ?int $page = null): CampaignsRequestBuilderGetQueryParameters {
        return new CampaignsRequestBuilderGetQueryParameters($branch, $page);
    }

}
