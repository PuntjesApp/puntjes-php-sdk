<?php

namespace Puntjes\Spike\Kiota\Transactions;

use Exception;
use Http\Promise\Promise;
use Microsoft\Kiota\Abstractions\BaseRequestBuilder;
use Microsoft\Kiota\Abstractions\HttpMethod;
use Microsoft\Kiota\Abstractions\RequestAdapter;
use Microsoft\Kiota\Abstractions\RequestInformation;
use Puntjes\Spike\Kiota\Models\CreateTransactionData;
use Puntjes\Spike\Kiota\Models\ErrorResponse;

/**
 * Builds and executes requests for operations under /transactions
*/
class TransactionsRequestBuilder extends BaseRequestBuilder 
{
    /**
     * Instantiates a new TransactionsRequestBuilder and sets the default values.
     * @param array<string, mixed>|string $pathParametersOrRawUrl Path parameters for the request or a String representing the raw URL.
     * @param RequestAdapter $requestAdapter The request adapter to use to execute the requests.
    */
    public function __construct($pathParametersOrRawUrl, RequestAdapter $requestAdapter) {
        parent::__construct($requestAdapter, [], '{+baseurl}/transactions');
        if (is_array($pathParametersOrRawUrl)) {
            $this->pathParameters = $pathParametersOrRawUrl;
        } else {
            $this->pathParameters = ['request-raw-url' => $pathParametersOrRawUrl];
        }
    }

    /**
     * @param CreateTransactionData $body The request body
     * @param TransactionsRequestBuilderPostRequestConfiguration|null $requestConfiguration Configuration for the request such as headers, query parameters, and middleware options.
     * @return Promise<TransactionsPostResponse|null>
     * @throws Exception
    */
    public function post(CreateTransactionData $body, ?TransactionsRequestBuilderPostRequestConfiguration $requestConfiguration = null): Promise {
        $requestInfo = $this->toPostRequestInformation($body, $requestConfiguration);
        $errorMappings = [
                '400' => [ErrorResponse::class, 'createFromDiscriminatorValue'],
                '401' => [ErrorResponse::class, 'createFromDiscriminatorValue'],
                '403' => [ErrorResponse::class, 'createFromDiscriminatorValue'],
                '404' => [ErrorResponse::class, 'createFromDiscriminatorValue'],
                '415' => [ErrorResponse::class, 'createFromDiscriminatorValue'],
                '422' => [ErrorResponse::class, 'createFromDiscriminatorValue'],
                '429' => [ErrorResponse::class, 'createFromDiscriminatorValue'],
                '500' => [ErrorResponse::class, 'createFromDiscriminatorValue'],
        ];
        return $this->requestAdapter->sendAsync($requestInfo, [TransactionsPostResponse::class, 'createFromDiscriminatorValue'], $errorMappings);
    }

    /**
     * @param CreateTransactionData $body The request body
     * @param TransactionsRequestBuilderPostRequestConfiguration|null $requestConfiguration Configuration for the request such as headers, query parameters, and middleware options.
     * @return RequestInformation
    */
    public function toPostRequestInformation(CreateTransactionData $body, ?TransactionsRequestBuilderPostRequestConfiguration $requestConfiguration = null): RequestInformation {
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
     * @return TransactionsRequestBuilder
    */
    public function withUrl(string $rawUrl): TransactionsRequestBuilder {
        return new TransactionsRequestBuilder($rawUrl, $this->requestAdapter);
    }

}
