<?php

namespace Puntjes\Spike\Kiota\Customers\ByExternalId;

use Microsoft\Kiota\Abstractions\BaseRequestBuilder;
use Microsoft\Kiota\Abstractions\RequestAdapter;
use Puntjes\Spike\Kiota\Customers\ByExternalId\Item\WithExternalItemRequestBuilder;

/**
 * Builds and executes requests for operations under /customers/by-external-id
*/
class ByExternalIdRequestBuilder extends BaseRequestBuilder 
{
    /**
     * Gets an item from the Puntjes/Spike/Kiota.customers.byExternalId.item collection
     * @param string $externalId Unique identifier of the item
     * @return WithExternalItemRequestBuilder
    */
    public function byExternalId(string $externalId): WithExternalItemRequestBuilder {
        $urlTplParams = $this->pathParameters;
        $urlTplParams['externalId'] = $externalId;
        return new WithExternalItemRequestBuilder($urlTplParams, $this->requestAdapter);
    }

    /**
     * Instantiates a new ByExternalIdRequestBuilder and sets the default values.
     * @param array<string, mixed>|string $pathParametersOrRawUrl Path parameters for the request or a String representing the raw URL.
     * @param RequestAdapter $requestAdapter The request adapter to use to execute the requests.
    */
    public function __construct($pathParametersOrRawUrl, RequestAdapter $requestAdapter) {
        parent::__construct($requestAdapter, [], '{+baseurl}/customers/by-external-id');
        if (is_array($pathParametersOrRawUrl)) {
            $this->pathParameters = $pathParametersOrRawUrl;
        } else {
            $this->pathParameters = ['request-raw-url' => $pathParametersOrRawUrl];
        }
    }

}
