<?php

namespace Puntjes\Spike\Kiota\Customers;

use Exception;
use Http\Promise\Promise;
use Microsoft\Kiota\Abstractions\BaseRequestBuilder;
use Microsoft\Kiota\Abstractions\HttpMethod;
use Microsoft\Kiota\Abstractions\RequestAdapter;
use Microsoft\Kiota\Abstractions\RequestInformation;
use Puntjes\Spike\Kiota\Customers\ByExternalId\ByExternalIdRequestBuilder;
use Puntjes\Spike\Kiota\Customers\Item\WithCustomerItemRequestBuilder;
use Puntjes\Spike\Kiota\Customers\LinkExternalId\LinkExternalIdRequestBuilder;
use Puntjes\Spike\Kiota\Customers\Lookup\LookupRequestBuilder;
use Puntjes\Spike\Kiota\Models\CreateCustomerData;
use Puntjes\Spike\Kiota\Models\ErrorResponse;

/**
 * Builds and executes requests for operations under /customers
*/
class CustomersRequestBuilder extends BaseRequestBuilder 
{
    /**
     * The byExternalId property
    */
    public function byExternalId(): ByExternalIdRequestBuilder {
        return new ByExternalIdRequestBuilder($this->pathParameters, $this->requestAdapter);
    }
    
    /**
     * The linkExternalId property
    */
    public function linkExternalId(): LinkExternalIdRequestBuilder {
        return new LinkExternalIdRequestBuilder($this->pathParameters, $this->requestAdapter);
    }
    
    /**
     * The lookup property
    */
    public function lookup(): LookupRequestBuilder {
        return new LookupRequestBuilder($this->pathParameters, $this->requestAdapter);
    }
    
    /**
     * Gets an item from the Puntjes/Spike/Kiota.customers.item collection
     * @param string $customer Unique identifier of the item
     * @return WithCustomerItemRequestBuilder
    */
    public function byCustomer(string $customer): WithCustomerItemRequestBuilder {
        $urlTplParams = $this->pathParameters;
        $urlTplParams['customer'] = $customer;
        return new WithCustomerItemRequestBuilder($urlTplParams, $this->requestAdapter);
    }

    /**
     * Instantiates a new CustomersRequestBuilder and sets the default values.
     * @param array<string, mixed>|string $pathParametersOrRawUrl Path parameters for the request or a String representing the raw URL.
     * @param RequestAdapter $requestAdapter The request adapter to use to execute the requests.
    */
    public function __construct($pathParametersOrRawUrl, RequestAdapter $requestAdapter) {
        parent::__construct($requestAdapter, [], '{+baseurl}/customers');
        if (is_array($pathParametersOrRawUrl)) {
            $this->pathParameters = $pathParametersOrRawUrl;
        } else {
            $this->pathParameters = ['request-raw-url' => $pathParametersOrRawUrl];
        }
    }

    /**
     * @param CreateCustomerData $body The request body
     * @param CustomersRequestBuilderPostRequestConfiguration|null $requestConfiguration Configuration for the request such as headers, query parameters, and middleware options.
     * @return Promise<CustomersPostResponse|null>
     * @throws Exception
    */
    public function post(CreateCustomerData $body, ?CustomersRequestBuilderPostRequestConfiguration $requestConfiguration = null): Promise {
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
        return $this->requestAdapter->sendAsync($requestInfo, [CustomersPostResponse::class, 'createFromDiscriminatorValue'], $errorMappings);
    }

    /**
     * @param CreateCustomerData $body The request body
     * @param CustomersRequestBuilderPostRequestConfiguration|null $requestConfiguration Configuration for the request such as headers, query parameters, and middleware options.
     * @return RequestInformation
    */
    public function toPostRequestInformation(CreateCustomerData $body, ?CustomersRequestBuilderPostRequestConfiguration $requestConfiguration = null): RequestInformation {
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
     * @return CustomersRequestBuilder
    */
    public function withUrl(string $rawUrl): CustomersRequestBuilder {
        return new CustomersRequestBuilder($rawUrl, $this->requestAdapter);
    }

}
