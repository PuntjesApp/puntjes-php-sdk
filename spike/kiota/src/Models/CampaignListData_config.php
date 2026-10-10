<?php

namespace Puntjes\Spike\Kiota\Models;

use Microsoft\Kiota\Abstractions\Serialization\ComposedTypeWrapper;
use Microsoft\Kiota\Abstractions\Serialization\Parsable;
use Microsoft\Kiota\Abstractions\Serialization\ParseNode;
use Microsoft\Kiota\Abstractions\Serialization\SerializationWriter;

/**
 * Composed type wrapper for classes AnniversaryCampaignConfigData, CampaignListData_configMember1, MomentCampaignConfigData, NthPurchaseCampaignConfigData, TransactionCampaignConfigData, WinBackCampaignConfigData
*/
class CampaignListData_config implements ComposedTypeWrapper, Parsable 
{
    /**
     * @var AnniversaryCampaignConfigData|null $anniversaryCampaignConfigData Composed type representation for type AnniversaryCampaignConfigData
    */
    private ?AnniversaryCampaignConfigData $anniversaryCampaignConfigData = null;
    
    /**
     * @var CampaignListData_configMember1|null $campaignListData_configMember1 Composed type representation for type CampaignListData_configMember1
    */
    private ?CampaignListData_configMember1 $campaignListData_configMember1 = null;
    
    /**
     * @var MomentCampaignConfigData|null $momentCampaignConfigData Composed type representation for type MomentCampaignConfigData
    */
    private ?MomentCampaignConfigData $momentCampaignConfigData = null;
    
    /**
     * @var NthPurchaseCampaignConfigData|null $nthPurchaseCampaignConfigData Composed type representation for type NthPurchaseCampaignConfigData
    */
    private ?NthPurchaseCampaignConfigData $nthPurchaseCampaignConfigData = null;
    
    /**
     * @var TransactionCampaignConfigData|null $transactionCampaignConfigData Composed type representation for type TransactionCampaignConfigData
    */
    private ?TransactionCampaignConfigData $transactionCampaignConfigData = null;
    
    /**
     * @var WinBackCampaignConfigData|null $winBackCampaignConfigData Composed type representation for type WinBackCampaignConfigData
    */
    private ?WinBackCampaignConfigData $winBackCampaignConfigData = null;
    
    /**
     * Creates a new instance of the appropriate class based on discriminator value
     * @param ParseNode $parseNode The parse node to use to read the discriminator value and create the object
     * @return CampaignListData_config
    */
    public static function createFromDiscriminatorValue(ParseNode $parseNode): CampaignListData_config {
        $result = new CampaignListData_config();
        $mappingValueNode = $parseNode->getChildNode("type");
        if ($mappingValueNode !== null) {
            $mappingValue = $mappingValueNode->getStringValue();
            if ('anniversary' === $mappingValue) {
                $result->setAnniversaryCampaignConfigData(new AnniversaryCampaignConfigData());
            } else if ('moment' === $mappingValue) {
                $result->setMomentCampaignConfigData(new MomentCampaignConfigData());
            } else if ('nth_purchase' === $mappingValue) {
                $result->setNthPurchaseCampaignConfigData(new NthPurchaseCampaignConfigData());
            } else if ('transaction' === $mappingValue) {
                $result->setTransactionCampaignConfigData(new TransactionCampaignConfigData());
            } else if ('win_back' === $mappingValue) {
                $result->setWinBackCampaignConfigData(new WinBackCampaignConfigData());
            }
        }
        return $result;
    }

    /**
     * Gets the AnniversaryCampaignConfigData property value. Composed type representation for type AnniversaryCampaignConfigData
     * @return AnniversaryCampaignConfigData|null
    */
    public function getAnniversaryCampaignConfigData(): ?AnniversaryCampaignConfigData {
        return $this->anniversaryCampaignConfigData;
    }

    /**
     * Gets the CampaignListData_configMember1 property value. Composed type representation for type CampaignListData_configMember1
     * @return CampaignListData_configMember1|null
    */
    public function getCampaignListDataConfigMember1(): ?CampaignListData_configMember1 {
        return $this->campaignListData_configMember1;
    }

    /**
     * The deserialization information for the current model
     * @return array<string, callable(ParseNode): void>
    */
    public function getFieldDeserializers(): array {
        if ($this->getAnniversaryCampaignConfigData() !== null) {
            return $this->getAnniversaryCampaignConfigData()->getFieldDeserializers();
        } else if ($this->getCampaignListDataConfigMember1() !== null) {
            return $this->getCampaignListDataConfigMember1()->getFieldDeserializers();
        } else if ($this->getMomentCampaignConfigData() !== null) {
            return $this->getMomentCampaignConfigData()->getFieldDeserializers();
        } else if ($this->getNthPurchaseCampaignConfigData() !== null) {
            return $this->getNthPurchaseCampaignConfigData()->getFieldDeserializers();
        } else if ($this->getTransactionCampaignConfigData() !== null) {
            return $this->getTransactionCampaignConfigData()->getFieldDeserializers();
        } else if ($this->getWinBackCampaignConfigData() !== null) {
            return $this->getWinBackCampaignConfigData()->getFieldDeserializers();
        }
        return [];
    }

    /**
     * Gets the MomentCampaignConfigData property value. Composed type representation for type MomentCampaignConfigData
     * @return MomentCampaignConfigData|null
    */
    public function getMomentCampaignConfigData(): ?MomentCampaignConfigData {
        return $this->momentCampaignConfigData;
    }

    /**
     * Gets the NthPurchaseCampaignConfigData property value. Composed type representation for type NthPurchaseCampaignConfigData
     * @return NthPurchaseCampaignConfigData|null
    */
    public function getNthPurchaseCampaignConfigData(): ?NthPurchaseCampaignConfigData {
        return $this->nthPurchaseCampaignConfigData;
    }

    /**
     * Gets the TransactionCampaignConfigData property value. Composed type representation for type TransactionCampaignConfigData
     * @return TransactionCampaignConfigData|null
    */
    public function getTransactionCampaignConfigData(): ?TransactionCampaignConfigData {
        return $this->transactionCampaignConfigData;
    }

    /**
     * Gets the WinBackCampaignConfigData property value. Composed type representation for type WinBackCampaignConfigData
     * @return WinBackCampaignConfigData|null
    */
    public function getWinBackCampaignConfigData(): ?WinBackCampaignConfigData {
        return $this->winBackCampaignConfigData;
    }

    /**
     * Serializes information the current object
     * @param SerializationWriter $writer Serialization writer to use to serialize this model
    */
    public function serialize(SerializationWriter $writer): void {
        if ($this->getAnniversaryCampaignConfigData() !== null) {
            $writer->writeObjectValue(null, $this->getAnniversaryCampaignConfigData());
        } else if ($this->getCampaignListDataConfigMember1() !== null) {
            $writer->writeObjectValue(null, $this->getCampaignListDataConfigMember1());
        } else if ($this->getMomentCampaignConfigData() !== null) {
            $writer->writeObjectValue(null, $this->getMomentCampaignConfigData());
        } else if ($this->getNthPurchaseCampaignConfigData() !== null) {
            $writer->writeObjectValue(null, $this->getNthPurchaseCampaignConfigData());
        } else if ($this->getTransactionCampaignConfigData() !== null) {
            $writer->writeObjectValue(null, $this->getTransactionCampaignConfigData());
        } else if ($this->getWinBackCampaignConfigData() !== null) {
            $writer->writeObjectValue(null, $this->getWinBackCampaignConfigData());
        }
    }

    /**
     * Sets the AnniversaryCampaignConfigData property value. Composed type representation for type AnniversaryCampaignConfigData
     * @param AnniversaryCampaignConfigData|null $value Value to set for the AnniversaryCampaignConfigData property.
    */
    public function setAnniversaryCampaignConfigData(?AnniversaryCampaignConfigData $value): void {
        $this->anniversaryCampaignConfigData = $value;
    }

    /**
     * Sets the CampaignListData_configMember1 property value. Composed type representation for type CampaignListData_configMember1
     * @param CampaignListData_configMember1|null $value Value to set for the CampaignListData_configMember1 property.
    */
    public function setCampaignListDataConfigMember1(?CampaignListData_configMember1 $value): void {
        $this->campaignListData_configMember1 = $value;
    }

    /**
     * Sets the MomentCampaignConfigData property value. Composed type representation for type MomentCampaignConfigData
     * @param MomentCampaignConfigData|null $value Value to set for the MomentCampaignConfigData property.
    */
    public function setMomentCampaignConfigData(?MomentCampaignConfigData $value): void {
        $this->momentCampaignConfigData = $value;
    }

    /**
     * Sets the NthPurchaseCampaignConfigData property value. Composed type representation for type NthPurchaseCampaignConfigData
     * @param NthPurchaseCampaignConfigData|null $value Value to set for the NthPurchaseCampaignConfigData property.
    */
    public function setNthPurchaseCampaignConfigData(?NthPurchaseCampaignConfigData $value): void {
        $this->nthPurchaseCampaignConfigData = $value;
    }

    /**
     * Sets the TransactionCampaignConfigData property value. Composed type representation for type TransactionCampaignConfigData
     * @param TransactionCampaignConfigData|null $value Value to set for the TransactionCampaignConfigData property.
    */
    public function setTransactionCampaignConfigData(?TransactionCampaignConfigData $value): void {
        $this->transactionCampaignConfigData = $value;
    }

    /**
     * Sets the WinBackCampaignConfigData property value. Composed type representation for type WinBackCampaignConfigData
     * @param WinBackCampaignConfigData|null $value Value to set for the WinBackCampaignConfigData property.
    */
    public function setWinBackCampaignConfigData(?WinBackCampaignConfigData $value): void {
        $this->winBackCampaignConfigData = $value;
    }

}
