<?php

namespace Puntjes\Spike\Kiota\Customers\Item\WalletPass;

use Exception;
use Http\Promise\Promise;
use Microsoft\Kiota\Abstractions\BaseRequestBuilder;
use Microsoft\Kiota\Abstractions\HttpMethod;
use Microsoft\Kiota\Abstractions\RequestAdapter;
use Microsoft\Kiota\Abstractions\RequestInformation;
use Puntjes\Spike\Kiota\Models\ErrorResponse;

/**
 * Builds and executes requests for operations under /customers/{customer}/wallet-pass
*/
class WalletPassRequestBuilder extends BaseRequestBuilder 
{
    /**
     * Instantiates a new WalletPassRequestBuilder and sets the default values.
     * @param array<string, mixed>|string $pathParametersOrRawUrl Path parameters for the request or a String representing the raw URL.
     * @param RequestAdapter $requestAdapter The request adapter to use to execute the requests.
    */
    public function __construct($pathParametersOrRawUrl, RequestAdapter $requestAdapter) {
        parent::__construct($requestAdapter, [], '{+baseurl}/customers/{customer}/wallet-pass?platform={platform}');
        if (is_array($pathParametersOrRawUrl)) {
            $this->pathParameters = $pathParametersOrRawUrl;
        } else {
            $this->pathParameters = ['request-raw-url' => $pathParametersOrRawUrl];
        }
    }

    /**
     * @param WalletPassRequestBuilderGetRequestConfiguration|null $requestConfiguration Configuration for the request such as headers, query parameters, and middleware options.
     * @return Promise<WalletPassGetResponse|null>
     * @throws Exception
    */
    public function get(?WalletPassRequestBuilderGetRequestConfiguration $requestConfiguration = null): Promise {
        $requestInfo = $this->toGetRequestInformation($requestConfiguration);
        $errorMappings = [
                '401' => [ErrorResponse::class, 'createFromDiscriminatorValue'],
                '403' => [ErrorResponse::class, 'createFromDiscriminatorValue'],
                '404' => [ErrorResponse::class, 'createFromDiscriminatorValue'],
                '422' => [ErrorResponse::class, 'createFromDiscriminatorValue'],
                '429' => [ErrorResponse::class, 'createFromDiscriminatorValue'],
                '500' => [ErrorResponse::class, 'createFromDiscriminatorValue'],
        ];
        return $this->requestAdapter->sendAsync($requestInfo, [WalletPassGetResponse::class, 'createFromDiscriminatorValue'], $errorMappings);
    }

    /**
     * @param WalletPassRequestBuilderGetRequestConfiguration|null $requestConfiguration Configuration for the request such as headers, query parameters, and middleware options.
     * @return RequestInformation
    */
    public function toGetRequestInformation(?WalletPassRequestBuilderGetRequestConfiguration $requestConfiguration = null): RequestInformation {
        $requestInfo = new RequestInformation();
        $requestInfo->urlTemplate = $this->urlTemplate;
        $requestInfo->pathParameters = $this->pathParameters;
        $requestInfo->httpMethod = HttpMethod::GET;
        if ($requestConfiguration !== null) {
            $requestInfo->addHeaders($requestConfiguration->headers);
            if ($requestConfiguration->queryParameters !== null) {
                $requestInfo->setQueryParameters($requestConfiguration->queryParameters);
            }
            $requestInfo->addRequestOptions(...$requestConfiguration->options);
        }
        $requestInfo->tryAddHeader('Accept', "application/json");
        return $requestInfo;
    }

    /**
     * Returns a request builder with the provided arbitrary URL. Using this method means any other path or query parameters are ignored.
     * @param string $rawUrl The raw URL to use for the request builder.
     * @return WalletPassRequestBuilder
    */
    public function withUrl(string $rawUrl): WalletPassRequestBuilder {
        return new WalletPassRequestBuilder($rawUrl, $this->requestAdapter);
    }

}
