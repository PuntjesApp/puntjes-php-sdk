<?php

namespace Puntjes\Spike\Kiota\Products;

use Exception;
use Http\Promise\Promise;
use Microsoft\Kiota\Abstractions\BaseRequestBuilder;
use Microsoft\Kiota\Abstractions\HttpMethod;
use Microsoft\Kiota\Abstractions\RequestAdapter;
use Microsoft\Kiota\Abstractions\RequestInformation;
use Puntjes\Spike\Kiota\Models\CreateProductData;
use Puntjes\Spike\Kiota\Models\ErrorResponse;
use Puntjes\Spike\Kiota\Products\Batch\BatchRequestBuilder;
use Puntjes\Spike\Kiota\Products\Item\WithExternalItemRequestBuilder;

/**
 * Builds and executes requests for operations under /products
*/
class ProductsRequestBuilder extends BaseRequestBuilder 
{
    /**
     * The batch property
    */
    public function batch(): BatchRequestBuilder {
        return new BatchRequestBuilder($this->pathParameters, $this->requestAdapter);
    }
    
    /**
     * Gets an item from the Puntjes/Spike/Kiota.products.item collection
     * @param string $externalId Unique identifier of the item
     * @return WithExternalItemRequestBuilder
    */
    public function byExternalId(string $externalId): WithExternalItemRequestBuilder {
        $urlTplParams = $this->pathParameters;
        $urlTplParams['externalId'] = $externalId;
        return new WithExternalItemRequestBuilder($urlTplParams, $this->requestAdapter);
    }

    /**
     * Instantiates a new ProductsRequestBuilder and sets the default values.
     * @param array<string, mixed>|string $pathParametersOrRawUrl Path parameters for the request or a String representing the raw URL.
     * @param RequestAdapter $requestAdapter The request adapter to use to execute the requests.
    */
    public function __construct($pathParametersOrRawUrl, RequestAdapter $requestAdapter) {
        parent::__construct($requestAdapter, [], '{+baseurl}/products{?category*,page*,per_page*,search*,status*}');
        if (is_array($pathParametersOrRawUrl)) {
            $this->pathParameters = $pathParametersOrRawUrl;
        } else {
            $this->pathParameters = ['request-raw-url' => $pathParametersOrRawUrl];
        }
    }

    /**
     * @param ProductsRequestBuilderGetRequestConfiguration|null $requestConfiguration Configuration for the request such as headers, query parameters, and middleware options.
     * @return Promise<ProductsGetResponse|null>
     * @throws Exception
    */
    public function get(?ProductsRequestBuilderGetRequestConfiguration $requestConfiguration = null): Promise {
        $requestInfo = $this->toGetRequestInformation($requestConfiguration);
        $errorMappings = [
                '401' => [ErrorResponse::class, 'createFromDiscriminatorValue'],
                '403' => [ErrorResponse::class, 'createFromDiscriminatorValue'],
                '422' => [ErrorResponse::class, 'createFromDiscriminatorValue'],
                '429' => [ErrorResponse::class, 'createFromDiscriminatorValue'],
                '500' => [ErrorResponse::class, 'createFromDiscriminatorValue'],
        ];
        return $this->requestAdapter->sendAsync($requestInfo, [ProductsGetResponse::class, 'createFromDiscriminatorValue'], $errorMappings);
    }

    /**
     * @param CreateProductData $body The request body
     * @param ProductsRequestBuilderPostRequestConfiguration|null $requestConfiguration Configuration for the request such as headers, query parameters, and middleware options.
     * @return Promise<ProductsPostResponse|null>
     * @throws Exception
    */
    public function post(CreateProductData $body, ?ProductsRequestBuilderPostRequestConfiguration $requestConfiguration = null): Promise {
        $requestInfo = $this->toPostRequestInformation($body, $requestConfiguration);
        $errorMappings = [
                '400' => [ErrorResponse::class, 'createFromDiscriminatorValue'],
                '401' => [ErrorResponse::class, 'createFromDiscriminatorValue'],
                '403' => [ErrorResponse::class, 'createFromDiscriminatorValue'],
                '409' => [ErrorResponse::class, 'createFromDiscriminatorValue'],
                '415' => [ErrorResponse::class, 'createFromDiscriminatorValue'],
                '422' => [ErrorResponse::class, 'createFromDiscriminatorValue'],
                '429' => [ErrorResponse::class, 'createFromDiscriminatorValue'],
                '500' => [ErrorResponse::class, 'createFromDiscriminatorValue'],
        ];
        return $this->requestAdapter->sendAsync($requestInfo, [ProductsPostResponse::class, 'createFromDiscriminatorValue'], $errorMappings);
    }

    /**
     * @param ProductsRequestBuilderGetRequestConfiguration|null $requestConfiguration Configuration for the request such as headers, query parameters, and middleware options.
     * @return RequestInformation
    */
    public function toGetRequestInformation(?ProductsRequestBuilderGetRequestConfiguration $requestConfiguration = null): RequestInformation {
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
     * @param CreateProductData $body The request body
     * @param ProductsRequestBuilderPostRequestConfiguration|null $requestConfiguration Configuration for the request such as headers, query parameters, and middleware options.
     * @return RequestInformation
    */
    public function toPostRequestInformation(CreateProductData $body, ?ProductsRequestBuilderPostRequestConfiguration $requestConfiguration = null): RequestInformation {
        $requestInfo = new RequestInformation();
        $requestInfo->urlTemplate = $this->urlTemplate;
        $requestInfo->pathParameters = $this->pathParameters;
        $requestInfo->httpMethod = HttpMethod::POST;
        if ($requestConfiguration !== null) {
            $requestInfo->addHeaders($requestConfiguration->headers);
            $requestInfo->addRequestOptions(...$requestConfiguration->options);
        }
        $requestInfo->tryAddHeader('Accept', "application/json");
        $requestInfo->setContentFromParsable($this->requestAdapter, "application/json", $body);
        return $requestInfo;
    }

    /**
     * Returns a request builder with the provided arbitrary URL. Using this method means any other path or query parameters are ignored.
     * @param string $rawUrl The raw URL to use for the request builder.
     * @return ProductsRequestBuilder
    */
    public function withUrl(string $rawUrl): ProductsRequestBuilder {
        return new ProductsRequestBuilder($rawUrl, $this->requestAdapter);
    }

}
