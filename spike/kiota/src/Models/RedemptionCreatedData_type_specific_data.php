<?php

namespace Puntjes\Spike\Kiota\Models;

use Microsoft\Kiota\Abstractions\Serialization\ComposedTypeWrapper;
use Microsoft\Kiota\Abstractions\Serialization\Parsable;
use Microsoft\Kiota\Abstractions\Serialization\ParseNode;
use Microsoft\Kiota\Abstractions\Serialization\SerializationWriter;

/**
 * Composed type wrapper for classes DiscountRedemptionData, FreeProductRedemptionData
*/
class RedemptionCreatedData_type_specific_data implements ComposedTypeWrapper, Parsable 
{
    /**
     * @var DiscountRedemptionData|null $discountRedemptionData Composed type representation for type DiscountRedemptionData
    */
    private ?DiscountRedemptionData $discountRedemptionData = null;
    
    /**
     * @var FreeProductRedemptionData|null $freeProductRedemptionData Composed type representation for type FreeProductRedemptionData
    */
    private ?FreeProductRedemptionData $freeProductRedemptionData = null;
    
    /**
     * Creates a new instance of the appropriate class based on discriminator value
     * @param ParseNode $parseNode The parse node to use to read the discriminator value and create the object
     * @return RedemptionCreatedData_type_specific_data
    */
    public static function createFromDiscriminatorValue(ParseNode $parseNode): RedemptionCreatedData_type_specific_data {
        $result = new RedemptionCreatedData_type_specific_data();
        $mappingValueNode = $parseNode->getChildNode("type");
        if ($mappingValueNode !== null) {
            $mappingValue = $mappingValueNode->getStringValue();
            if ('discount' === $mappingValue) {
                $result->setDiscountRedemptionData(new DiscountRedemptionData());
            } else if ('free_product' === $mappingValue) {
                $result->setFreeProductRedemptionData(new FreeProductRedemptionData());
            }
        }
        return $result;
    }

    /**
     * Gets the DiscountRedemptionData property value. Composed type representation for type DiscountRedemptionData
     * @return DiscountRedemptionData|null
    */
    public function getDiscountRedemptionData(): ?DiscountRedemptionData {
        return $this->discountRedemptionData;
    }

    /**
     * The deserialization information for the current model
     * @return array<string, callable(ParseNode): void>
    */
    public function getFieldDeserializers(): array {
        if ($this->getDiscountRedemptionData() !== null) {
            return $this->getDiscountRedemptionData()->getFieldDeserializers();
        } else if ($this->getFreeProductRedemptionData() !== null) {
            return $this->getFreeProductRedemptionData()->getFieldDeserializers();
        }
        return [];
    }

    /**
     * Gets the FreeProductRedemptionData property value. Composed type representation for type FreeProductRedemptionData
     * @return FreeProductRedemptionData|null
    */
    public function getFreeProductRedemptionData(): ?FreeProductRedemptionData {
        return $this->freeProductRedemptionData;
    }

    /**
     * Serializes information the current object
     * @param SerializationWriter $writer Serialization writer to use to serialize this model
    */
    public function serialize(SerializationWriter $writer): void {
        if ($this->getDiscountRedemptionData() !== null) {
            $writer->writeObjectValue(null, $this->getDiscountRedemptionData());
        } else if ($this->getFreeProductRedemptionData() !== null) {
            $writer->writeObjectValue(null, $this->getFreeProductRedemptionData());
        }
    }

    /**
     * Sets the DiscountRedemptionData property value. Composed type representation for type DiscountRedemptionData
     * @param DiscountRedemptionData|null $value Value to set for the DiscountRedemptionData property.
    */
    public function setDiscountRedemptionData(?DiscountRedemptionData $value): void {
        $this->discountRedemptionData = $value;
    }

    /**
     * Sets the FreeProductRedemptionData property value. Composed type representation for type FreeProductRedemptionData
     * @param FreeProductRedemptionData|null $value Value to set for the FreeProductRedemptionData property.
    */
    public function setFreeProductRedemptionData(?FreeProductRedemptionData $value): void {
        $this->freeProductRedemptionData = $value;
    }

}
