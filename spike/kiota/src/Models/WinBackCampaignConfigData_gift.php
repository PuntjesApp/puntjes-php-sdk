<?php

namespace Puntjes\Spike\Kiota\Models;

use Microsoft\Kiota\Abstractions\Serialization\ComposedTypeWrapper;
use Microsoft\Kiota\Abstractions\Serialization\Parsable;
use Microsoft\Kiota\Abstractions\Serialization\ParseNode;
use Microsoft\Kiota\Abstractions\Serialization\SerializationWriter;

/**
 * Composed type wrapper for classes DiscountGiftData, FreeProductGiftData, PointsGiftData
*/
class WinBackCampaignConfigData_gift implements ComposedTypeWrapper, Parsable 
{
    /**
     * @var DiscountGiftData|null $discountGiftData Composed type representation for type DiscountGiftData
    */
    private ?DiscountGiftData $discountGiftData = null;
    
    /**
     * @var FreeProductGiftData|null $freeProductGiftData Composed type representation for type FreeProductGiftData
    */
    private ?FreeProductGiftData $freeProductGiftData = null;
    
    /**
     * @var PointsGiftData|null $pointsGiftData Composed type representation for type PointsGiftData
    */
    private ?PointsGiftData $pointsGiftData = null;
    
    /**
     * Creates a new instance of the appropriate class based on discriminator value
     * @param ParseNode $parseNode The parse node to use to read the discriminator value and create the object
     * @return WinBackCampaignConfigData_gift
    */
    public static function createFromDiscriminatorValue(ParseNode $parseNode): WinBackCampaignConfigData_gift {
        $result = new WinBackCampaignConfigData_gift();
        $mappingValueNode = $parseNode->getChildNode("type");
        if ($mappingValueNode !== null) {
            $mappingValue = $mappingValueNode->getStringValue();
            if ('discount' === $mappingValue) {
                $result->setDiscountGiftData(new DiscountGiftData());
            } else if ('free_product' === $mappingValue) {
                $result->setFreeProductGiftData(new FreeProductGiftData());
            } else if ('points' === $mappingValue) {
                $result->setPointsGiftData(new PointsGiftData());
            }
        }
        return $result;
    }

    /**
     * Gets the DiscountGiftData property value. Composed type representation for type DiscountGiftData
     * @return DiscountGiftData|null
    */
    public function getDiscountGiftData(): ?DiscountGiftData {
        return $this->discountGiftData;
    }

    /**
     * The deserialization information for the current model
     * @return array<string, callable(ParseNode): void>
    */
    public function getFieldDeserializers(): array {
        if ($this->getDiscountGiftData() !== null) {
            return $this->getDiscountGiftData()->getFieldDeserializers();
        } else if ($this->getFreeProductGiftData() !== null) {
            return $this->getFreeProductGiftData()->getFieldDeserializers();
        } else if ($this->getPointsGiftData() !== null) {
            return $this->getPointsGiftData()->getFieldDeserializers();
        }
        return [];
    }

    /**
     * Gets the FreeProductGiftData property value. Composed type representation for type FreeProductGiftData
     * @return FreeProductGiftData|null
    */
    public function getFreeProductGiftData(): ?FreeProductGiftData {
        return $this->freeProductGiftData;
    }

    /**
     * Gets the PointsGiftData property value. Composed type representation for type PointsGiftData
     * @return PointsGiftData|null
    */
    public function getPointsGiftData(): ?PointsGiftData {
        return $this->pointsGiftData;
    }

    /**
     * Serializes information the current object
     * @param SerializationWriter $writer Serialization writer to use to serialize this model
    */
    public function serialize(SerializationWriter $writer): void {
        if ($this->getDiscountGiftData() !== null) {
            $writer->writeObjectValue(null, $this->getDiscountGiftData());
        } else if ($this->getFreeProductGiftData() !== null) {
            $writer->writeObjectValue(null, $this->getFreeProductGiftData());
        } else if ($this->getPointsGiftData() !== null) {
            $writer->writeObjectValue(null, $this->getPointsGiftData());
        }
    }

    /**
     * Sets the DiscountGiftData property value. Composed type representation for type DiscountGiftData
     * @param DiscountGiftData|null $value Value to set for the DiscountGiftData property.
    */
    public function setDiscountGiftData(?DiscountGiftData $value): void {
        $this->discountGiftData = $value;
    }

    /**
     * Sets the FreeProductGiftData property value. Composed type representation for type FreeProductGiftData
     * @param FreeProductGiftData|null $value Value to set for the FreeProductGiftData property.
    */
    public function setFreeProductGiftData(?FreeProductGiftData $value): void {
        $this->freeProductGiftData = $value;
    }

    /**
     * Sets the PointsGiftData property value. Composed type representation for type PointsGiftData
     * @param PointsGiftData|null $value Value to set for the PointsGiftData property.
    */
    public function setPointsGiftData(?PointsGiftData $value): void {
        $this->pointsGiftData = $value;
    }

}
