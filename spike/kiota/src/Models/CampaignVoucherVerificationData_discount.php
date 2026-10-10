<?php

namespace Puntjes\Spike\Kiota\Models;

use Microsoft\Kiota\Abstractions\Serialization\ComposedTypeWrapper;
use Microsoft\Kiota\Abstractions\Serialization\Parsable;
use Microsoft\Kiota\Abstractions\Serialization\ParseNode;
use Microsoft\Kiota\Abstractions\Serialization\SerializationWriter;

/**
 * Composed type wrapper for classes CampaignVoucherVerificationData_discountMember1, VoucherFixedDiscountData, VoucherPercentageDiscountData
*/
class CampaignVoucherVerificationData_discount implements ComposedTypeWrapper, Parsable 
{
    /**
     * @var CampaignVoucherVerificationData_discountMember1|null $campaignVoucherVerificationData_discountMember1 Composed type representation for type CampaignVoucherVerificationData_discountMember1
    */
    private ?CampaignVoucherVerificationData_discountMember1 $campaignVoucherVerificationData_discountMember1 = null;
    
    /**
     * @var VoucherFixedDiscountData|null $voucherFixedDiscountData Composed type representation for type VoucherFixedDiscountData
    */
    private ?VoucherFixedDiscountData $voucherFixedDiscountData = null;
    
    /**
     * @var VoucherPercentageDiscountData|null $voucherPercentageDiscountData Composed type representation for type VoucherPercentageDiscountData
    */
    private ?VoucherPercentageDiscountData $voucherPercentageDiscountData = null;
    
    /**
     * Creates a new instance of the appropriate class based on discriminator value
     * @param ParseNode $parseNode The parse node to use to read the discriminator value and create the object
     * @return CampaignVoucherVerificationData_discount
    */
    public static function createFromDiscriminatorValue(ParseNode $parseNode): CampaignVoucherVerificationData_discount {
        $result = new CampaignVoucherVerificationData_discount();
        $mappingValueNode = $parseNode->getChildNode("type");
        if ($mappingValueNode !== null) {
            $mappingValue = $mappingValueNode->getStringValue();
            if ('fixed' === $mappingValue) {
                $result->setVoucherFixedDiscountData(new VoucherFixedDiscountData());
            } else if ('percentage' === $mappingValue) {
                $result->setVoucherPercentageDiscountData(new VoucherPercentageDiscountData());
            }
        }
        return $result;
    }

    /**
     * Gets the CampaignVoucherVerificationData_discountMember1 property value. Composed type representation for type CampaignVoucherVerificationData_discountMember1
     * @return CampaignVoucherVerificationData_discountMember1|null
    */
    public function getCampaignVoucherVerificationDataDiscountMember1(): ?CampaignVoucherVerificationData_discountMember1 {
        return $this->campaignVoucherVerificationData_discountMember1;
    }

    /**
     * The deserialization information for the current model
     * @return array<string, callable(ParseNode): void>
    */
    public function getFieldDeserializers(): array {
        if ($this->getCampaignVoucherVerificationDataDiscountMember1() !== null) {
            return $this->getCampaignVoucherVerificationDataDiscountMember1()->getFieldDeserializers();
        } else if ($this->getVoucherFixedDiscountData() !== null) {
            return $this->getVoucherFixedDiscountData()->getFieldDeserializers();
        } else if ($this->getVoucherPercentageDiscountData() !== null) {
            return $this->getVoucherPercentageDiscountData()->getFieldDeserializers();
        }
        return [];
    }

    /**
     * Gets the VoucherFixedDiscountData property value. Composed type representation for type VoucherFixedDiscountData
     * @return VoucherFixedDiscountData|null
    */
    public function getVoucherFixedDiscountData(): ?VoucherFixedDiscountData {
        return $this->voucherFixedDiscountData;
    }

    /**
     * Gets the VoucherPercentageDiscountData property value. Composed type representation for type VoucherPercentageDiscountData
     * @return VoucherPercentageDiscountData|null
    */
    public function getVoucherPercentageDiscountData(): ?VoucherPercentageDiscountData {
        return $this->voucherPercentageDiscountData;
    }

    /**
     * Serializes information the current object
     * @param SerializationWriter $writer Serialization writer to use to serialize this model
    */
    public function serialize(SerializationWriter $writer): void {
        if ($this->getCampaignVoucherVerificationDataDiscountMember1() !== null) {
            $writer->writeObjectValue(null, $this->getCampaignVoucherVerificationDataDiscountMember1());
        } else if ($this->getVoucherFixedDiscountData() !== null) {
            $writer->writeObjectValue(null, $this->getVoucherFixedDiscountData());
        } else if ($this->getVoucherPercentageDiscountData() !== null) {
            $writer->writeObjectValue(null, $this->getVoucherPercentageDiscountData());
        }
    }

    /**
     * Sets the CampaignVoucherVerificationData_discountMember1 property value. Composed type representation for type CampaignVoucherVerificationData_discountMember1
     * @param CampaignVoucherVerificationData_discountMember1|null $value Value to set for the CampaignVoucherVerificationData_discountMember1 property.
    */
    public function setCampaignVoucherVerificationDataDiscountMember1(?CampaignVoucherVerificationData_discountMember1 $value): void {
        $this->campaignVoucherVerificationData_discountMember1 = $value;
    }

    /**
     * Sets the VoucherFixedDiscountData property value. Composed type representation for type VoucherFixedDiscountData
     * @param VoucherFixedDiscountData|null $value Value to set for the VoucherFixedDiscountData property.
    */
    public function setVoucherFixedDiscountData(?VoucherFixedDiscountData $value): void {
        $this->voucherFixedDiscountData = $value;
    }

    /**
     * Sets the VoucherPercentageDiscountData property value. Composed type representation for type VoucherPercentageDiscountData
     * @param VoucherPercentageDiscountData|null $value Value to set for the VoucherPercentageDiscountData property.
    */
    public function setVoucherPercentageDiscountData(?VoucherPercentageDiscountData $value): void {
        $this->voucherPercentageDiscountData = $value;
    }

}
