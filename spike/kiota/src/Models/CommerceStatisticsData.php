<?php

namespace Puntjes\Spike\Kiota\Models;

use Microsoft\Kiota\Abstractions\Serialization\AdditionalDataHolder;
use Microsoft\Kiota\Abstractions\Serialization\Parsable;
use Microsoft\Kiota\Abstractions\Serialization\ParseNode;
use Microsoft\Kiota\Abstractions\Serialization\SerializationWriter;

class CommerceStatisticsData implements AdditionalDataHolder, Parsable 
{
    /**
     * @var array<string, mixed>|null $additionalData Stores additional data not described in the OpenAPI description found when deserializing. Can be used for serialization as well.
    */
    private ?array $additionalData = null;
    
    /**
     * @var int|null $average_order_value_cents The average_order_value_cents property
    */
    private ?int $average_order_value_cents = null;
    
    /**
     * @var ItemizedStatisticsData|null $itemized The itemized property
    */
    private ?ItemizedStatisticsData $itemized = null;
    
    /**
     * @var int|null $orders The orders property
    */
    private ?int $orders = null;
    
    /**
     * @var int|null $revenue_cents The revenue_cents property
    */
    private ?int $revenue_cents = null;
    
    /**
     * @var array<VolumeBucketData>|null $volume_trend The volume_trend property
    */
    private ?array $volume_trend = null;
    
    /**
     * Instantiates a new CommerceStatisticsData and sets the default values.
    */
    public function __construct() {
        $this->setAdditionalData([]);
    }

    /**
     * Creates a new instance of the appropriate class based on discriminator value
     * @param ParseNode $parseNode The parse node to use to read the discriminator value and create the object
     * @return CommerceStatisticsData
    */
    public static function createFromDiscriminatorValue(ParseNode $parseNode): CommerceStatisticsData {
        return new CommerceStatisticsData();
    }

    /**
     * Gets the AdditionalData property value. Stores additional data not described in the OpenAPI description found when deserializing. Can be used for serialization as well.
     * @return array<string, mixed>|null
    */
    public function getAdditionalData(): ?array {
        return $this->additionalData;
    }

    /**
     * Gets the average_order_value_cents property value. The average_order_value_cents property
     * @return int|null
    */
    public function getAverageOrderValueCents(): ?int {
        return $this->average_order_value_cents;
    }

    /**
     * The deserialization information for the current model
     * @return array<string, callable(ParseNode): void>
    */
    public function getFieldDeserializers(): array {
        $o = $this;
        return  [
            'average_order_value_cents' => fn(ParseNode $n) => $o->setAverageOrderValueCents($n->getIntegerValue()),
            'itemized' => fn(ParseNode $n) => $o->setItemized($n->getObjectValue([ItemizedStatisticsData::class, 'createFromDiscriminatorValue'])),
            'orders' => fn(ParseNode $n) => $o->setOrders($n->getIntegerValue()),
            'revenue_cents' => fn(ParseNode $n) => $o->setRevenueCents($n->getIntegerValue()),
            'volume_trend' => fn(ParseNode $n) => $o->setVolumeTrend($n->getCollectionOfObjectValues([VolumeBucketData::class, 'createFromDiscriminatorValue'])),
        ];
    }

    /**
     * Gets the itemized property value. The itemized property
     * @return ItemizedStatisticsData|null
    */
    public function getItemized(): ?ItemizedStatisticsData {
        return $this->itemized;
    }

    /**
     * Gets the orders property value. The orders property
     * @return int|null
    */
    public function getOrders(): ?int {
        return $this->orders;
    }

    /**
     * Gets the revenue_cents property value. The revenue_cents property
     * @return int|null
    */
    public function getRevenueCents(): ?int {
        return $this->revenue_cents;
    }

    /**
     * Gets the volume_trend property value. The volume_trend property
     * @return array<VolumeBucketData>|null
    */
    public function getVolumeTrend(): ?array {
        return $this->volume_trend;
    }

    /**
     * Serializes information the current object
     * @param SerializationWriter $writer Serialization writer to use to serialize this model
    */
    public function serialize(SerializationWriter $writer): void {
        $writer->writeIntegerValue('average_order_value_cents', $this->getAverageOrderValueCents());
        $writer->writeObjectValue('itemized', $this->getItemized());
        $writer->writeIntegerValue('orders', $this->getOrders());
        $writer->writeIntegerValue('revenue_cents', $this->getRevenueCents());
        $writer->writeCollectionOfObjectValues('volume_trend', $this->getVolumeTrend());
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
     * Sets the average_order_value_cents property value. The average_order_value_cents property
     * @param int|null $value Value to set for the average_order_value_cents property.
    */
    public function setAverageOrderValueCents(?int $value): void {
        $this->average_order_value_cents = $value;
    }

    /**
     * Sets the itemized property value. The itemized property
     * @param ItemizedStatisticsData|null $value Value to set for the itemized property.
    */
    public function setItemized(?ItemizedStatisticsData $value): void {
        $this->itemized = $value;
    }

    /**
     * Sets the orders property value. The orders property
     * @param int|null $value Value to set for the orders property.
    */
    public function setOrders(?int $value): void {
        $this->orders = $value;
    }

    /**
     * Sets the revenue_cents property value. The revenue_cents property
     * @param int|null $value Value to set for the revenue_cents property.
    */
    public function setRevenueCents(?int $value): void {
        $this->revenue_cents = $value;
    }

    /**
     * Sets the volume_trend property value. The volume_trend property
     * @param array<VolumeBucketData>|null $value Value to set for the volume_trend property.
    */
    public function setVolumeTrend(?array $value): void {
        $this->volume_trend = $value;
    }

}
