<?php

namespace Puntjes\Spike\Kiota\Vouchers;

use Microsoft\Kiota\Abstractions\BaseRequestBuilder;
use Microsoft\Kiota\Abstractions\RequestAdapter;
use Puntjes\Spike\Kiota\Vouchers\Item\WithCodeItemRequestBuilder;

/**
 * Builds and executes requests for operations under /vouchers
*/
class VouchersRequestBuilder extends BaseRequestBuilder 
{
    /**
     * Gets an item from the Puntjes/Spike/Kiota.vouchers.item collection
     * @param string $code Unique identifier of the item
     * @return WithCodeItemRequestBuilder
    */
    public function byCode(string $code): WithCodeItemRequestBuilder {
        $urlTplParams = $this->pathParameters;
        $urlTplParams['code'] = $code;
        return new WithCodeItemRequestBuilder($urlTplParams, $this->requestAdapter);
    }

    /**
     * Instantiates a new VouchersRequestBuilder and sets the default values.
     * @param array<string, mixed>|string $pathParametersOrRawUrl Path parameters for the request or a String representing the raw URL.
     * @param RequestAdapter $requestAdapter The request adapter to use to execute the requests.
    */
    public function __construct($pathParametersOrRawUrl, RequestAdapter $requestAdapter) {
        parent::__construct($requestAdapter, [], '{+baseurl}/vouchers');
        if (is_array($pathParametersOrRawUrl)) {
            $this->pathParameters = $pathParametersOrRawUrl;
        } else {
            $this->pathParameters = ['request-raw-url' => $pathParametersOrRawUrl];
        }
    }

}
