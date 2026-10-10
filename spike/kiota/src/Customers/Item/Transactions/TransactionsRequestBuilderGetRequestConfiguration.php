<?php

namespace Puntjes\Spike\Kiota\Customers\Item\Transactions;

use Microsoft\Kiota\Abstractions\BaseRequestConfiguration;
use Microsoft\Kiota\Abstractions\RequestOption;

/**
 * Configuration for the request such as headers, query parameters, and middleware options.
*/
class TransactionsRequestBuilderGetRequestConfiguration extends BaseRequestConfiguration 
{
    /**
     * @var TransactionsRequestBuilderGetQueryParameters|null $queryParameters Request query parameters
    */
    public ?TransactionsRequestBuilderGetQueryParameters $queryParameters = null;
    
    /**
     * Instantiates a new TransactionsRequestBuilderGetRequestConfiguration and sets the default values.
     * @param array<string, array<string>|string>|null $headers Request headers
     * @param array<RequestOption>|null $options Request options
     * @param TransactionsRequestBuilderGetQueryParameters|null $queryParameters Request query parameters
    */
    public function __construct(?array $headers = null, ?array $options = null, ?TransactionsRequestBuilderGetQueryParameters $queryParameters = null) {
        parent::__construct($headers ?? [], $options ?? []);
        $this->queryParameters = $queryParameters;
    }

    /**
     * Instantiates a new TransactionsRequestBuilderGetQueryParameters.
     * @param string|null $date_from 
     * @param string|null $date_to 
     * @param int|null $page 
     * @return TransactionsRequestBuilderGetQueryParameters
    */
    public static function createQueryParameters(?string $date_from = null, ?string $date_to = null, ?int $page = null): TransactionsRequestBuilderGetQueryParameters {
        return new TransactionsRequestBuilderGetQueryParameters($date_from, $date_to, $page);
    }

}
