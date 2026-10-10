<?php

namespace Puntjes\Spike\Kiota\Models;

use Microsoft\Kiota\Abstractions\Serialization\ComposedTypeWrapper;
use Microsoft\Kiota\Abstractions\Serialization\Parsable;
use Microsoft\Kiota\Abstractions\Serialization\ParseNode;
use Microsoft\Kiota\Abstractions\Serialization\SerializationWriter;

/**
 * Composed type wrapper for classes FixedDiscountData, PercentageDiscountData
*/
class DiscountGiftData_discount implements ComposedTypeWrapper, Parsable 
{
    /**
     * @var FixedDiscountData|null $fixedDiscountData Composed type representation for type FixedDiscountData
    */
    private ?FixedDiscountData $fixedDiscountData = null;
    
    /**
     * @var PercentageDiscountData|null $percentageDiscountData Composed type representation for type PercentageDiscountData
    */
    private ?PercentageDiscountData $percentageDiscountData = null;
    
    /**
     * Creates a new instance of the appropriate class based on discriminator value
     * @param ParseNode $parseNode The parse node to use to read the discriminator value and create the object
     * @return DiscountGiftData_discount
    */
    public static function createFromDiscriminatorValue(ParseNode $parseNode): DiscountGiftData_discount {
        $result = new DiscountGiftData_discount();
        $mappingValueNode = $parseNode->getChildNode("type");
        if ($mappingValueNode !== null) {
            $mappingValue = $mappingValueNode->getStringValue();
            if ('fixed' === $mappingValue) {
                $result->setFixedDiscountData(new FixedDiscountData());
            } else if ('percentage' === $mappingValue) {
                $result->setPercentageDiscountData(new PercentageDiscountData());
            }
        }
        return $result;
    }

    /**
     * The deserialization information for the current model
     * @return array<string, callable(ParseNode): void>
    */
    public function getFieldDeserializers(): array {
        if ($this->getFixedDiscountData() !== null) {
            return $this->getFixedDiscountData()->getFieldDeserializers();
        } else if ($this->getPercentageDiscountData() !== null) {
            return $this->getPercentageDiscountData()->getFieldDeserializers();
        }
        return [];
    }

    /**
     * Gets the FixedDiscountData property value. Composed type representation for type FixedDiscountData
     * @return FixedDiscountData|null
    */
    public function getFixedDiscountData(): ?FixedDiscountData {
        return $this->fixedDiscountData;
    }

    /**
     * Gets the PercentageDiscountData property value. Composed type representation for type PercentageDiscountData
     * @return PercentageDiscountData|null
    */
    public function getPercentageDiscountData(): ?PercentageDiscountData {
        return $this->percentageDiscountData;
    }

    /**
     * Serializes information the current object
     * @param SerializationWriter $writer Serialization writer to use to serialize this model
    */
    public function serialize(SerializationWriter $writer): void {
        if ($this->getFixedDiscountData() !== null) {
            $writer->writeObjectValue(null, $this->getFixedDiscountData());
        } else if ($this->getPercentageDiscountData() !== null) {
            $writer->writeObjectValue(null, $this->getPercentageDiscountData());
        }
    }

    /**
     * Sets the FixedDiscountData property value. Composed type representation for type FixedDiscountData
     * @param FixedDiscountData|null $value Value to set for the FixedDiscountData property.
    */
    public function setFixedDiscountData(?FixedDiscountData $value): void {
        $this->fixedDiscountData = $value;
    }

    /**
     * Sets the PercentageDiscountData property value. Composed type representation for type PercentageDiscountData
     * @param PercentageDiscountData|null $value Value to set for the PercentageDiscountData property.
    */
    public function setPercentageDiscountData(?PercentageDiscountData $value): void {
        $this->percentageDiscountData = $value;
    }

}
