<?php

namespace Puntjes\Spike\Kiota\Models;

use Microsoft\Kiota\Abstractions\Serialization\AdditionalDataHolder;
use Microsoft\Kiota\Abstractions\Serialization\Parsable;
use Microsoft\Kiota\Abstractions\Serialization\ParseNode;
use Microsoft\Kiota\Abstractions\Serialization\SerializationWriter;

class CategoryRevenueData implements AdditionalDataHolder, Parsable 
{
    /**
     * @var array<string, mixed>|null $additionalData Stores additional data not described in the OpenAPI description found when deserializing. Can be used for serialization as well.
    */
    private ?array $additionalData = null;
    
    /**
     * @var string|null $category The category property
    */
    private ?string $category = null;
    
    /**
     * @var bool|null $isOther The isOther property
    */
    private ?bool $isOther = null;
    
    /**
     * @var int|null $revenue The revenue property
    */
    private ?int $revenue = null;
    
    /**
     * @var int|null $units The units property
    */
    private ?int $units = null;
    
    /**
     * Instantiates a new CategoryRevenueData and sets the default values.
    */
    public function __construct() {
        $this->setAdditionalData([]);
    }

    /**
     * Creates a new instance of the appropriate class based on discriminator value
     * @param ParseNode $parseNode The parse node to use to read the discriminator value and create the object
     * @return CategoryRevenueData
    */
    public static function createFromDiscriminatorValue(ParseNode $parseNode): CategoryRevenueData {
        return new CategoryRevenueData();
    }

    /**
     * Gets the AdditionalData property value. Stores additional data not described in the OpenAPI description found when deserializing. Can be used for serialization as well.
     * @return array<string, mixed>|null
    */
    public function getAdditionalData(): ?array {
        return $this->additionalData;
    }

    /**
     * Gets the category property value. The category property
     * @return string|null
    */
    public function getCategory(): ?string {
        return $this->category;
    }

    /**
     * The deserialization information for the current model
     * @return array<string, callable(ParseNode): void>
    */
    public function getFieldDeserializers(): array {
        $o = $this;
        return  [
            'category' => fn(ParseNode $n) => $o->setCategory($n->getStringValue()),
            'isOther' => fn(ParseNode $n) => $o->setIsOther($n->getBooleanValue()),
            'revenue' => fn(ParseNode $n) => $o->setRevenue($n->getIntegerValue()),
            'units' => fn(ParseNode $n) => $o->setUnits($n->getIntegerValue()),
        ];
    }

    /**
     * Gets the isOther property value. The isOther property
     * @return bool|null
    */
    public function getIsOther(): ?bool {
        return $this->isOther;
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
        $writer->writeStringValue('category', $this->getCategory());
        $writer->writeBooleanValue('isOther', $this->getIsOther());
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
     * Sets the category property value. The category property
     * @param string|null $value Value to set for the category property.
    */
    public function setCategory(?string $value): void {
        $this->category = $value;
    }

    /**
     * Sets the isOther property value. The isOther property
     * @param bool|null $value Value to set for the isOther property.
    */
    public function setIsOther(?bool $value): void {
        $this->isOther = $value;
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
