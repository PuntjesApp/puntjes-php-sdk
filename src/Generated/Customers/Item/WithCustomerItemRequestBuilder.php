<?php

namespace Puntjes\Generated\Customers\Item;

use Exception;
use Http\Promise\Promise;
use Microsoft\Kiota\Abstractions\BaseRequestBuilder;
use Microsoft\Kiota\Abstractions\HttpMethod;
use Microsoft\Kiota\Abstractions\RequestAdapter;
use Microsoft\Kiota\Abstractions\RequestInformation;
use Puntjes\Generated\Customers\Item\ExternalId\ExternalIdRequestBuilder;
use Puntjes\Generated\Customers\Item\Ledger\LedgerRequestBuilder;
use Puntjes\Generated\Customers\Item\Merge\MergeRequestBuilder;
use Puntjes\Generated\Customers\Item\Redemptions\RedemptionsRequestBuilder;
use Puntjes\Generated\Customers\Item\SendCard\SendCardRequestBuilder;
use Puntjes\Generated\Customers\Item\Transactions\TransactionsRequestBuilder;
use Puntjes\Generated\Customers\Item\Vouchers\VouchersRequestBuilder;
use Puntjes\Generated\Customers\Item\Wallet\WalletRequestBuilder;
use Puntjes\Generated\Customers\Item\WalletPass\WalletPassRequestBuilder;
use Puntjes\Generated\Models\ErrorResponse;

/**
 * Builds and executes requests for operations under /customers/{customer}
*/
class WithCustomerItemRequestBuilder extends BaseRequestBuilder 
{
    /**
     * The externalId property
    */
    public function externalId(): ExternalIdRequestBuilder {
        return new ExternalIdRequestBuilder($this->pathParameters, $this->requestAdapter);
    }
    
    /**
     * The ledger property
    */
    public function ledger(): LedgerRequestBuilder {
        return new LedgerRequestBuilder($this->pathParameters, $this->requestAdapter);
    }
    
    /**
     * The merge property
    */
    public function merge(): MergeRequestBuilder {
        return new MergeRequestBuilder($this->pathParameters, $this->requestAdapter);
    }
    
    /**
     * The redemptions property
    */
    public function redemptions(): RedemptionsRequestBuilder {
        return new RedemptionsRequestBuilder($this->pathParameters, $this->requestAdapter);
    }
    
    /**
     * The sendCard property
    */
    public function sendCard(): SendCardRequestBuilder {
        return new SendCardRequestBuilder($this->pathParameters, $this->requestAdapter);
    }
    
    /**
     * The transactions property
    */
    public function transactions(): TransactionsRequestBuilder {
        return new TransactionsRequestBuilder($this->pathParameters, $this->requestAdapter);
    }
    
    /**
     * The vouchers property
    */
    public function vouchers(): VouchersRequestBuilder {
        return new VouchersRequestBuilder($this->pathParameters, $this->requestAdapter);
    }
    
    /**
     * The wallet property
    */
    public function wallet(): WalletRequestBuilder {
        return new WalletRequestBuilder($this->pathParameters, $this->requestAdapter);
    }
    
    /**
     * The walletPass property
    */
    public function walletPass(): WalletPassRequestBuilder {
        return new WalletPassRequestBuilder($this->pathParameters, $this->requestAdapter);
    }
    
    /**
     * Instantiates a new WithCustomerItemRequestBuilder and sets the default values.
     * @param array<string, mixed>|string $pathParametersOrRawUrl Path parameters for the request or a String representing the raw URL.
     * @param RequestAdapter $requestAdapter The request adapter to use to execute the requests.
    */
    public function __construct($pathParametersOrRawUrl, RequestAdapter $requestAdapter) {
        parent::__construct($requestAdapter, [], '{+baseurl}/customers/{customer}');
        if (is_array($pathParametersOrRawUrl)) {
            $this->pathParameters = $pathParametersOrRawUrl;
        } else {
            $this->pathParameters = ['request-raw-url' => $pathParametersOrRawUrl];
        }
    }

    /**
     * @param WithCustomerItemRequestBuilderGetRequestConfiguration|null $requestConfiguration Configuration for the request such as headers, query parameters, and middleware options.
     * @return Promise<WithCustomerGetResponse|null>
     * @throws Exception
    */
    public function get(?WithCustomerItemRequestBuilderGetRequestConfiguration $requestConfiguration = null): Promise {
        $requestInfo = $this->toGetRequestInformation($requestConfiguration);
        $errorMappings = [
                '401' => [ErrorResponse::class, 'createFromDiscriminatorValue'],
                '403' => [ErrorResponse::class, 'createFromDiscriminatorValue'],
                '404' => [ErrorResponse::class, 'createFromDiscriminatorValue'],
                '429' => [ErrorResponse::class, 'createFromDiscriminatorValue'],
                '500' => [ErrorResponse::class, 'createFromDiscriminatorValue'],
        ];
        return $this->requestAdapter->sendAsync($requestInfo, [WithCustomerGetResponse::class, 'createFromDiscriminatorValue'], $errorMappings);
    }

    /**
     * @param WithCustomerItemRequestBuilderGetRequestConfiguration|null $requestConfiguration Configuration for the request such as headers, query parameters, and middleware options.
     * @return RequestInformation
    */
    public function toGetRequestInformation(?WithCustomerItemRequestBuilderGetRequestConfiguration $requestConfiguration = null): RequestInformation {
        $requestInfo = new RequestInformation();
        $requestInfo->urlTemplate = $this->urlTemplate;
        $requestInfo->pathParameters = $this->pathParameters;
        $requestInfo->httpMethod = HttpMethod::GET;
        if ($requestConfiguration !== null) {
            $requestInfo->addHeaders($requestConfiguration->headers);
            $requestInfo->addRequestOptions(...$requestConfiguration->options);
        }
        $requestInfo->tryAddHeader('Accept', "application/json");
        return $requestInfo;
    }

    /**
     * Returns a request builder with the provided arbitrary URL. Using this method means any other path or query parameters are ignored.
     * @param string $rawUrl The raw URL to use for the request builder.
     * @return WithCustomerItemRequestBuilder
    */
    public function withUrl(string $rawUrl): WithCustomerItemRequestBuilder {
        return new WithCustomerItemRequestBuilder($rawUrl, $this->requestAdapter);
    }

}
