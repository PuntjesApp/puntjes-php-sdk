<?php

namespace Puntjes\Spike\Kiota\Customers\Item\Ledger;

use Microsoft\Kiota\Abstractions\BaseRequestConfiguration;
use Microsoft\Kiota\Abstractions\RequestOption;

/**
 * Configuration for the request such as headers, query parameters, and middleware options.
*/
class LedgerRequestBuilderGetRequestConfiguration extends BaseRequestConfiguration 
{
    /**
     * @var LedgerRequestBuilderGetQueryParameters|null $queryParameters Request query parameters
    */
    public ?LedgerRequestBuilderGetQueryParameters $queryParameters = null;
    
    /**
     * Instantiates a new LedgerRequestBuilderGetRequestConfiguration and sets the default values.
     * @param array<string, array<string>|string>|null $headers Request headers
     * @param array<RequestOption>|null $options Request options
     * @param LedgerRequestBuilderGetQueryParameters|null $queryParameters Request query parameters
    */
    public function __construct(?array $headers = null, ?array $options = null, ?LedgerRequestBuilderGetQueryParameters $queryParameters = null) {
        parent::__construct($headers ?? [], $options ?? []);
        $this->queryParameters = $queryParameters;
    }

    /**
     * Instantiates a new LedgerRequestBuilderGetQueryParameters.
     * @param string|null $date_from 
     * @param string|null $date_to 
     * @param int|null $page 
     * @param GetTypeQueryParameterType|null $type 
     * @return LedgerRequestBuilderGetQueryParameters
    */
    public static function createQueryParameters(?string $date_from = null, ?string $date_to = null, ?int $page = null, ?GetTypeQueryParameterType $type = null): LedgerRequestBuilderGetQueryParameters {
        return new LedgerRequestBuilderGetQueryParameters($date_from, $date_to, $page, $type);
    }

}
