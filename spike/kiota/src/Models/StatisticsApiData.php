<?php

namespace Puntjes\Spike\Kiota\Models;

use Microsoft\Kiota\Abstractions\Serialization\AdditionalDataHolder;
use Microsoft\Kiota\Abstractions\Serialization\Parsable;
use Microsoft\Kiota\Abstractions\Serialization\ParseNode;
use Microsoft\Kiota\Abstractions\Serialization\SerializationWriter;

class StatisticsApiData implements AdditionalDataHolder, Parsable 
{
    /**
     * @var array<string, mixed>|null $additionalData Stores additional data not described in the OpenAPI description found when deserializing. Can be used for serialization as well.
    */
    private ?array $additionalData = null;
    
    /**
     * @var CommerceStatisticsData|null $commerce The commerce property
    */
    private ?CommerceStatisticsData $commerce = null;
    
    /**
     * @var LoyaltyStatisticsData|null $loyalty The loyalty property
    */
    private ?LoyaltyStatisticsData $loyalty = null;
    
    /**
     * @var StatisticsPeriodData|null $period The period property
    */
    private ?StatisticsPeriodData $period = null;
    
    /**
     * Instantiates a new StatisticsApiData and sets the default values.
    */
    public function __construct() {
        $this->setAdditionalData([]);
    }

    /**
     * Creates a new instance of the appropriate class based on discriminator value
     * @param ParseNode $parseNode The parse node to use to read the discriminator value and create the object
     * @return StatisticsApiData
    */
    public static function createFromDiscriminatorValue(ParseNode $parseNode): StatisticsApiData {
        return new StatisticsApiData();
    }

    /**
     * Gets the AdditionalData property value. Stores additional data not described in the OpenAPI description found when deserializing. Can be used for serialization as well.
     * @return array<string, mixed>|null
    */
    public function getAdditionalData(): ?array {
        return $this->additionalData;
    }

    /**
     * Gets the commerce property value. The commerce property
     * @return CommerceStatisticsData|null
    */
    public function getCommerce(): ?CommerceStatisticsData {
        return $this->commerce;
    }

    /**
     * The deserialization information for the current model
     * @return array<string, callable(ParseNode): void>
    */
    public function getFieldDeserializers(): array {
        $o = $this;
        return  [
            'commerce' => fn(ParseNode $n) => $o->setCommerce($n->getObjectValue([CommerceStatisticsData::class, 'createFromDiscriminatorValue'])),
            'loyalty' => fn(ParseNode $n) => $o->setLoyalty($n->getObjectValue([LoyaltyStatisticsData::class, 'createFromDiscriminatorValue'])),
            'period' => fn(ParseNode $n) => $o->setPeriod($n->getObjectValue([StatisticsPeriodData::class, 'createFromDiscriminatorValue'])),
        ];
    }

    /**
     * Gets the loyalty property value. The loyalty property
     * @return LoyaltyStatisticsData|null
    */
    public function getLoyalty(): ?LoyaltyStatisticsData {
        return $this->loyalty;
    }

    /**
     * Gets the period property value. The period property
     * @return StatisticsPeriodData|null
    */
    public function getPeriod(): ?StatisticsPeriodData {
        return $this->period;
    }

    /**
     * Serializes information the current object
     * @param SerializationWriter $writer Serialization writer to use to serialize this model
    */
    public function serialize(SerializationWriter $writer): void {
        $writer->writeObjectValue('commerce', $this->getCommerce());
        $writer->writeObjectValue('loyalty', $this->getLoyalty());
        $writer->writeObjectValue('period', $this->getPeriod());
        $writer->writeAdditionalData($this->getAdditionalData());
    }

    /**
     * Sets the AdditionalData property value. Stores additional data not described in the OpenAPI description found when deserializing. Can be used for serialization as well.
     * @param array<string,mixed> $value Value to set for the AdditionalData property.
    */
    public function setAdditionalData(?array $value): void {
        $this->additionalData = $value;
    }

    /**
     * Sets the commerce property value. The commerce property
     * @param CommerceStatisticsData|null $value Value to set for the commerce property.
    */
    public function setCommerce(?CommerceStatisticsData $value): void {
        $this->commerce = $value;
    }

    /**
     * Sets the loyalty property value. The loyalty property
     * @param LoyaltyStatisticsData|null $value Value to set for the loyalty property.
    */
    public function setLoyalty(?LoyaltyStatisticsData $value): void {
        $this->loyalty = $value;
    }

    /**
     * Sets the period property value. The period property
     * @param StatisticsPeriodData|null $value Value to set for the period property.
    */
    public function setPeriod(?StatisticsPeriodData $value): void {
        $this->period = $value;
    }

}
