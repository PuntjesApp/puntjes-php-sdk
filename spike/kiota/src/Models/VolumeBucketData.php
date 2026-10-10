<?php

namespace Puntjes\Spike\Kiota\Models;

use Microsoft\Kiota\Abstractions\Serialization\AdditionalDataHolder;
use Microsoft\Kiota\Abstractions\Serialization\Parsable;
use Microsoft\Kiota\Abstractions\Serialization\ParseNode;
use Microsoft\Kiota\Abstractions\Serialization\SerializationWriter;

class VolumeBucketData implements AdditionalDataHolder, Parsable 
{
    /**
     * @var array<string, mixed>|null $additionalData Stores additional data not described in the OpenAPI description found when deserializing. Can be used for serialization as well.
    */
    private ?array $additionalData = null;
    
    /**
     * @var string|null $label The label property
    */
    private ?string $label = null;
    
    /**
     * @var int|null $orders The orders property
    */
    private ?int $orders = null;
    
    /**
     * @var int|null $revenue The revenue property
    */
    private ?int $revenue = null;
    
    /**
     * Instantiates a new VolumeBucketData and sets the default values.
    */
    public function __construct() {
        $this->setAdditionalData([]);
    }

    /**
     * Creates a new instance of the appropriate class based on discriminator value
     * @param ParseNode $parseNode The parse node to use to read the discriminator value and create the object
     * @return VolumeBucketData
    */
    public static function createFromDiscriminatorValue(ParseNode $parseNode): VolumeBucketData {
        return new VolumeBucketData();
    }

    /**
     * Gets the AdditionalData property value. Stores additional data not described in the OpenAPI description found when deserializing. Can be used for serialization as well.
     * @return array<string, mixed>|null
    */
    public function getAdditionalData(): ?array {
        return $this->additionalData;
    }

    /**
     * The deserialization information for the current model
     * @return array<string, callable(ParseNode): void>
    */
    public function getFieldDeserializers(): array {
        $o = $this;
        return  [
            'label' => fn(ParseNode $n) => $o->setLabel($n->getStringValue()),
            'orders' => fn(ParseNode $n) => $o->setOrders($n->getIntegerValue()),
            'revenue' => fn(ParseNode $n) => $o->setRevenue($n->getIntegerValue()),
        ];
    }

    /**
     * Gets the label property value. The label property
     * @return string|null
    */
    public function getLabel(): ?string {
        return $this->label;
    }

    /**
     * Gets the orders property value. The orders property
     * @return int|null
    */
    public function getOrders(): ?int {
        return $this->orders;
    }

    /**
     * Gets the revenue property value. The revenue property
     * @return int|null
    */
    public function getRevenue(): ?int {
        return $this->revenue;
    }

    /**
     * Serializes information the current object
     * @param SerializationWriter $writer Serialization writer to use to serialize this model
    */
    public function serialize(SerializationWriter $writer): void {
        $writer->writeStringValue('label', $this->getLabel());
        $writer->writeIntegerValue('orders', $this->getOrders());
        $writer->writeIntegerValue('revenue', $this->getRevenue());
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
     * Sets the label property value. The label property
     * @param string|null $value Value to set for the label property.
    */
    public function setLabel(?string $value): void {
        $this->label = $value;
    }

    /**
     * Sets the orders property value. The orders property
     * @param int|null $value Value to set for the orders property.
    */
    public function setOrders(?int $value): void {
        $this->orders = $value;
    }

    /**
     * Sets the revenue property value. The revenue property
     * @param int|null $value Value to set for the revenue property.
    */
    public function setRevenue(?int $value): void {
        $this->revenue = $value;
    }

}
