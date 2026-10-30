<?php

namespace Puntjes\Generated\Client;

use Exception;
use Http\Promise\Promise;
use Microsoft\Kiota\Abstractions\BaseRequestBuilder;
use Microsoft\Kiota\Abstractions\HttpMethod;
use Microsoft\Kiota\Abstractions\RequestAdapter;
use Microsoft\Kiota\Abstractions\RequestInformation;
use Puntjes\Generated\Client\Secret\SecretRequestBuilder;
use Puntjes\Generated\Models\ClientSecretData;
use Puntjes\Generated\Models\ErrorResponse;

/**
 * Builds and executes requests for operations under /client
*/
class ClientRequestBuilder extends BaseRequestBuilder 
{
    /**
     * The secret property
    */
    public function secret(): SecretRequestBuilder {
        return new SecretRequestBuilder($this->pathParameters, $this->requestAdapter);
    }
    
    /**
     * Instantiates a new ClientRequestBuilder and sets the default values.
     * @param array<string, mixed>|string $pathParametersOrRawUrl Path parameters for the request or a String representing the raw URL.
     * @param RequestAdapter $requestAdapter The request adapter to use to execute the requests.
    */
    public function __construct($pathParametersOrRawUrl, RequestAdapter $requestAdapter) {
        parent::__construct($requestAdapter, [], '{+baseurl}/client');
        if (is_array($pathParametersOrRawUrl)) {
            $this->pathParameters = $pathParametersOrRawUrl;
        } else {
            $this->pathParameters = ['request-raw-url' => $pathParametersOrRawUrl];
        }
    }

    /**
     * @param ClientSecretData $body The request body
     * @param ClientRequestBuilderDeleteRequestConfiguration|null $requestConfiguration Configuration for the request such as headers, query parameters, and middleware options.
     * @return Promise<void|null>
     * @throws Exception
    */
    public function delete(ClientSecretData $body, ?ClientRequestBuilderDeleteRequestConfiguration $requestConfiguration = null): Promise {
        $requestInfo = $this->toDeleteRequestInformation($body, $requestConfiguration);
        $errorMappings = [
                '400' => [ErrorResponse::class, 'createFromDiscriminatorValue'],
                '401' => [ErrorResponse::class, 'createFromDiscriminatorValue'],
                '403' => [ErrorResponse::class, 'createFromDiscriminatorValue'],
                '415' => [ErrorResponse::class, 'createFromDiscriminatorValue'],
                '422' => [ErrorResponse::class, 'createFromDiscriminatorValue'],
                '429' => [ErrorResponse::class, 'createFromDiscriminatorValue'],
                '500' => [ErrorResponse::class, 'createFromDiscriminatorValue'],
        ];
        return $this->requestAdapter->sendNoContentAsync($requestInfo, $errorMappings);
    }

    /**
     * @param ClientSecretData $body The request body
     * @param ClientRequestBuilderDeleteRequestConfiguration|null $requestConfiguration Configuration for the request such as headers, query parameters, and middleware options.
     * @return RequestInformation
    */
    public function toDeleteRequestInformation(ClientSecretData $body, ?ClientRequestBuilderDeleteRequestConfiguration $requestConfiguration = null): RequestInformation {
        $requestInfo = new RequestInformation();
        $requestInfo->urlTemplate = $this->urlTemplate;
        $requestInfo->pathParameters = $this->pathParameters;
        $requestInfo->httpMethod = HttpMethod::DELETE;
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
     * @return ClientRequestBuilder
    */
    public function withUrl(string $rawUrl): ClientRequestBuilder {
        return new ClientRequestBuilder($rawUrl, $this->requestAdapter);
    }

}
