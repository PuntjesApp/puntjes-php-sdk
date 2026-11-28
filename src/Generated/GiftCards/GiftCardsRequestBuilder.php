<?php

namespace Puntjes\Generated\GiftCards;

use Microsoft\Kiota\Abstractions\BaseRequestBuilder;
use Microsoft\Kiota\Abstractions\RequestAdapter;
use Puntjes\Generated\GiftCards\Load\LoadRequestBuilder;
use Puntjes\Generated\GiftCards\Lookup\LookupRequestBuilder;
use Puntjes\Generated\GiftCards\Redeem\RedeemRequestBuilder;

/**
 * Builds and executes requests for operations under /gift-cards
*/
class GiftCardsRequestBuilder extends BaseRequestBuilder 
{
    /**
     * The load property
    */
    public function load(): LoadRequestBuilder {
        return new LoadRequestBuilder($this->pathParameters, $this->requestAdapter);
    }
    
    /**
     * The lookup property
    */
    public function lookup(): LookupRequestBuilder {
        return new LookupRequestBuilder($this->pathParameters, $this->requestAdapter);
    }
    
    /**
     * The redeem property
    */
    public function redeem(): RedeemRequestBuilder {
        return new RedeemRequestBuilder($this->pathParameters, $this->requestAdapter);
    }
    
    /**
     * Instantiates a new GiftCardsRequestBuilder and sets the default values.
     * @param array<string, mixed>|string $pathParametersOrRawUrl Path parameters for the request or a String representing the raw URL.
     * @param RequestAdapter $requestAdapter The request adapter to use to execute the requests.
    */
    public function __construct($pathParametersOrRawUrl, RequestAdapter $requestAdapter) {
        parent::__construct($requestAdapter, [], '{+baseurl}/gift-cards');
        if (is_array($pathParametersOrRawUrl)) {
            $this->pathParameters = $pathParametersOrRawUrl;
        } else {
            $this->pathParameters = ['request-raw-url' => $pathParametersOrRawUrl];
        }
    }

}
