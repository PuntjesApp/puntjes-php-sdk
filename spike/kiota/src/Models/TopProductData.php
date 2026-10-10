<?php

namespace Puntjes\Spike\Kiota\Models;

use Microsoft\Kiota\Abstractions\Serialization\AdditionalDataHolder;
use Microsoft\Kiota\Abstractions\Serialization\Parsable;
use Microsoft\Kiota\Abstractions\Serialization\ParseNode;
use Microsoft\Kiota\Abstractions\Serialization\SerializationWriter;

class TopProductData implements AdditionalDataHolder, Parsable 
{
    /**
     * @var array<string, mixed>|null $additionalData Stores additional data not described in the OpenAPI description found when deserializing. Can be used for serialization as well.
    */
    private ?array $additionalData = null;
    
    /**
     * @var string|null $name The name property
    */
    private ?string $name = null;
    
    /**
     * @var int|null $revenue The revenue property
    */
    private ?int $revenue = null;
    
    /**
     * @var int|null $units The units property
    */
    private ?int $units = null;
    
    /**
     * Instantiates a new TopProductData and sets the default values.
    */
    public function __construct() {
        $this->setAdditionalData([]);
    }

    /**
     * Creates a new instance of the appropriate class based on discriminator value
     * @param ParseNode $parseNode The parse node to use to read the discriminator value and create the object
     * @return TopProductData
    */
    public static function createFromDiscriminatorValue(ParseNode $parseNode): TopProductData {
        return new TopProductData();
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
            'name' => fn(ParseNode $n) => $o->setName($n->getStringValue()),
            'revenue' => fn(ParseNode $n) => $o->setRevenue($n->getIntegerValue()),
            'units' => fn(ParseNode $n) => $o->setUnits($n->getIntegerValue()),
        ];
    }

    /**
     * Gets the name property value. The name property
     * @return string|null
    */
    public function getName(): ?string {
        return $this->name;
    }

    /**
     * Gets the revenue property value. The revenue property
     * @return int|null
    */
    public function getRevenue(): ?int {
        return $this->revenue;
    }

    /**
     * Gets the units property value. The units property
     * @return int|null
    */
    public function getUnits(): ?int {
        return $this->units;
    }

    /**
     * Serializes information the current object
     * @param SerializationWriter $writer Serialization writer to use to serialize this model
    */
    public function serialize(SerializationWriter $writer): void {
        $writer->writeStringValue('name', $this->getName());
        $writer->writeIntegerValue('revenue', $this->getRevenue());
        $writer->writeIntegerValue('units', $this->getUnits());
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
     * Sets the name property value. The name property
     * @param string|null $value Value to set for the name property.
    */
    public function setName(?string $value): void {
        $this->name = $value;
    }

    /**
     * Sets the revenue property value. The revenue property
     * @param int|null $value Value to set for the revenue property.
    */
    public function setRevenue(?int $value): void {
        $this->revenue = $value;
    }

    /**
     * Sets the units property value. The units property
     * @param int|null $value Value to set for the units property.
    */
    public function setUnits(?int $value): void {
        $this->units = $value;
    }

}
