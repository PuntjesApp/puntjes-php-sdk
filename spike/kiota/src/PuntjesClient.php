<?php

namespace Puntjes\Spike\Kiota;

use Microsoft\Kiota\Abstractions\ApiClientBuilder;
use Microsoft\Kiota\Abstractions\BaseRequestBuilder;
use Microsoft\Kiota\Abstractions\RequestAdapter;
use Microsoft\Kiota\Serialization\Form\FormParseNodeFactory;
use Microsoft\Kiota\Serialization\Form\FormSerializationWriterFactory;
use Microsoft\Kiota\Serialization\Json\JsonParseNodeFactory;
use Microsoft\Kiota\Serialization\Json\JsonSerializationWriterFactory;
use Microsoft\Kiota\Serialization\Multipart\MultipartSerializationWriterFactory;
use Microsoft\Kiota\Serialization\Text\TextParseNodeFactory;
use Microsoft\Kiota\Serialization\Text\TextSerializationWriterFactory;
use Puntjes\Spike\Kiota\Campaigns\CampaignsRequestBuilder;
use Puntjes\Spike\Kiota\Customers\CustomersRequestBuilder;
use Puntjes\Spike\Kiota\Health\HealthRequestBuilder;
use Puntjes\Spike\Kiota\Me\MeRequestBuilder;
use Puntjes\Spike\Kiota\Products\ProductsRequestBuilder;
use Puntjes\Spike\Kiota\Redemptions\RedemptionsRequestBuilder;
use Puntjes\Spike\Kiota\Rewards\RewardsRequestBuilder;
use Puntjes\Spike\Kiota\Statistics\StatisticsRequestBuilder;
use Puntjes\Spike\Kiota\Transactions\TransactionsRequestBuilder;
use Puntjes\Spike\Kiota\Vouchers\VouchersRequestBuilder;

/**
 * The main entry point of the SDK, exposes the configuration and the fluent API.
*/
class PuntjesClient extends BaseRequestBuilder 
{
    /**
     * The campaigns property
    */
    public function campaigns(): CampaignsRequestBuilder {
        return new CampaignsRequestBuilder($this->pathParameters, $this->requestAdapter);
    }
    
    /**
     * The customers property
    */
    public function customers(): CustomersRequestBuilder {
        return new CustomersRequestBuilder($this->pathParameters, $this->requestAdapter);
    }
    
    /**
     * The health property
    */
    public function health(): HealthRequestBuilder {
        return new HealthRequestBuilder($this->pathParameters, $this->requestAdapter);
    }
    
    /**
     * The me property
    */
    public function me(): MeRequestBuilder {
        return new MeRequestBuilder($this->pathParameters, $this->requestAdapter);
    }
    
    /**
     * The products property
    */
    public function products(): ProductsRequestBuilder {
        return new ProductsRequestBuilder($this->pathParameters, $this->requestAdapter);
    }
    
    /**
     * The redemptions property
    */
    public function redemptions(): RedemptionsRequestBuilder {
        return new RedemptionsRequestBuilder($this->pathParameters, $this->requestAdapter);
    }
    
    /**
     * The rewards property
    */
    public function rewards(): RewardsRequestBuilder {
        return new RewardsRequestBuilder($this->pathParameters, $this->requestAdapter);
    }
    
    /**
     * The statistics property
    */
    public function statistics(): StatisticsRequestBuilder {
        return new StatisticsRequestBuilder($this->pathParameters, $this->requestAdapter);
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
     * Instantiates a new PuntjesClient and sets the default values.
     * @param RequestAdapter $requestAdapter The request adapter to use to execute the requests.
    */
    public function __construct(RequestAdapter $requestAdapter) {
        parent::__construct($requestAdapter, [], '{+baseurl}');
        ApiClientBuilder::registerDefaultSerializer(JsonSerializationWriterFactory::class);
        ApiClientBuilder::registerDefaultSerializer(TextSerializationWriterFactory::class);
        ApiClientBuilder::registerDefaultSerializer(FormSerializationWriterFactory::class);
        ApiClientBuilder::registerDefaultSerializer(MultipartSerializationWriterFactory::class);
        ApiClientBuilder::registerDefaultDeserializer(JsonParseNodeFactory::class);
        ApiClientBuilder::registerDefaultDeserializer(TextParseNodeFactory::class);
        ApiClientBuilder::registerDefaultDeserializer(FormParseNodeFactory::class);
        if (empty($this->requestAdapter->getBaseUrl())) {
            $this->requestAdapter->setBaseUrl('https://puntjes.app/api/v1');
        }
        $this->pathParameters['baseurl'] = $this->requestAdapter->getBaseUrl();
    }

}
