<?php

namespace Puntjes\Spike\Kiota\Models;

use Microsoft\Kiota\Abstractions\Serialization\ComposedTypeWrapper;
use Microsoft\Kiota\Abstractions\Serialization\Parsable;
use Microsoft\Kiota\Abstractions\Serialization\ParseNode;
use Microsoft\Kiota\Abstractions\Serialization\SerializationWriter;

/**
 * Composed type wrapper for classes CampaignListData_recurrence_configMember1, DateRangeRecurrenceData, DaysOfWeekRecurrenceData, SpecificDatesRecurrenceData
*/
class CampaignListData_recurrence_config implements ComposedTypeWrapper, Parsable 
{
    /**
     * @var CampaignListData_recurrence_configMember1|null $campaignListData_recurrence_configMember1 Composed type representation for type CampaignListData_recurrence_configMember1
    */
    private ?CampaignListData_recurrence_configMember1 $campaignListData_recurrence_configMember1 = null;
    
    /**
     * @var DateRangeRecurrenceData|null $dateRangeRecurrenceData Composed type representation for type DateRangeRecurrenceData
    */
    private ?DateRangeRecurrenceData $dateRangeRecurrenceData = null;
    
    /**
     * @var DaysOfWeekRecurrenceData|null $daysOfWeekRecurrenceData Composed type representation for type DaysOfWeekRecurrenceData
    */
    private ?DaysOfWeekRecurrenceData $daysOfWeekRecurrenceData = null;
    
    /**
     * @var SpecificDatesRecurrenceData|null $specificDatesRecurrenceData Composed type representation for type SpecificDatesRecurrenceData
    */
    private ?SpecificDatesRecurrenceData $specificDatesRecurrenceData = null;
    
    /**
     * Creates a new instance of the appropriate class based on discriminator value
     * @param ParseNode $parseNode The parse node to use to read the discriminator value and create the object
     * @return CampaignListData_recurrence_config
    */
    public static function createFromDiscriminatorValue(ParseNode $parseNode): CampaignListData_recurrence_config {
        $result = new CampaignListData_recurrence_config();
        $mappingValueNode = $parseNode->getChildNode("type");
        if ($mappingValueNode !== null) {
            $mappingValue = $mappingValueNode->getStringValue();
            if ('date_range' === $mappingValue) {
                $result->setDateRangeRecurrenceData(new DateRangeRecurrenceData());
            } else if ('days_of_week' === $mappingValue) {
                $result->setDaysOfWeekRecurrenceData(new DaysOfWeekRecurrenceData());
            } else if ('specific_dates' === $mappingValue) {
                $result->setSpecificDatesRecurrenceData(new SpecificDatesRecurrenceData());
            }
        }
        return $result;
    }

    /**
     * Gets the CampaignListData_recurrence_configMember1 property value. Composed type representation for type CampaignListData_recurrence_configMember1
     * @return CampaignListData_recurrence_configMember1|null
    */
    public function getCampaignListDataRecurrenceConfigMember1(): ?CampaignListData_recurrence_configMember1 {
        return $this->campaignListData_recurrence_configMember1;
    }

    /**
     * Gets the DateRangeRecurrenceData property value. Composed type representation for type DateRangeRecurrenceData
     * @return DateRangeRecurrenceData|null
    */
    public function getDateRangeRecurrenceData(): ?DateRangeRecurrenceData {
        return $this->dateRangeRecurrenceData;
    }

    /**
     * Gets the DaysOfWeekRecurrenceData property value. Composed type representation for type DaysOfWeekRecurrenceData
     * @return DaysOfWeekRecurrenceData|null
    */
    public function getDaysOfWeekRecurrenceData(): ?DaysOfWeekRecurrenceData {
        return $this->daysOfWeekRecurrenceData;
    }

    /**
     * The deserialization information for the current model
     * @return array<string, callable(ParseNode): void>
    */
    public function getFieldDeserializers(): array {
        if ($this->getCampaignListDataRecurrenceConfigMember1() !== null) {
            return $this->getCampaignListDataRecurrenceConfigMember1()->getFieldDeserializers();
        } else if ($this->getDateRangeRecurrenceData() !== null) {
            return $this->getDateRangeRecurrenceData()->getFieldDeserializers();
        } else if ($this->getDaysOfWeekRecurrenceData() !== null) {
            return $this->getDaysOfWeekRecurrenceData()->getFieldDeserializers();
        } else if ($this->getSpecificDatesRecurrenceData() !== null) {
            return $this->getSpecificDatesRecurrenceData()->getFieldDeserializers();
        }
        return [];
    }

    /**
     * Gets the SpecificDatesRecurrenceData property value. Composed type representation for type SpecificDatesRecurrenceData
     * @return SpecificDatesRecurrenceData|null
    */
    public function getSpecificDatesRecurrenceData(): ?SpecificDatesRecurrenceData {
        return $this->specificDatesRecurrenceData;
    }

    /**
     * Serializes information the current object
     * @param SerializationWriter $writer Serialization writer to use to serialize this model
    */
    public function serialize(SerializationWriter $writer): void {
        if ($this->getCampaignListDataRecurrenceConfigMember1() !== null) {
            $writer->writeObjectValue(null, $this->getCampaignListDataRecurrenceConfigMember1());
        } else if ($this->getDateRangeRecurrenceData() !== null) {
            $writer->writeObjectValue(null, $this->getDateRangeRecurrenceData());
        } else if ($this->getDaysOfWeekRecurrenceData() !== null) {
            $writer->writeObjectValue(null, $this->getDaysOfWeekRecurrenceData());
        } else if ($this->getSpecificDatesRecurrenceData() !== null) {
            $writer->writeObjectValue(null, $this->getSpecificDatesRecurrenceData());
        }
    }

    /**
     * Sets the CampaignListData_recurrence_configMember1 property value. Composed type representation for type CampaignListData_recurrence_configMember1
     * @param CampaignListData_recurrence_configMember1|null $value Value to set for the CampaignListData_recurrence_configMember1 property.
    */
    public function setCampaignListDataRecurrenceConfigMember1(?CampaignListData_recurrence_configMember1 $value): void {
        $this->campaignListData_recurrence_configMember1 = $value;
    }

    /**
     * Sets the DateRangeRecurrenceData property value. Composed type representation for type DateRangeRecurrenceData
     * @param DateRangeRecurrenceData|null $value Value to set for the DateRangeRecurrenceData property.
    */
    public function setDateRangeRecurrenceData(?DateRangeRecurrenceData $value): void {
        $this->dateRangeRecurrenceData = $value;
    }

    /**
     * Sets the DaysOfWeekRecurrenceData property value. Composed type representation for type DaysOfWeekRecurrenceData
     * @param DaysOfWeekRecurrenceData|null $value Value to set for the DaysOfWeekRecurrenceData property.
    */
    public function setDaysOfWeekRecurrenceData(?DaysOfWeekRecurrenceData $value): void {
        $this->daysOfWeekRecurrenceData = $value;
    }

    /**
     * Sets the SpecificDatesRecurrenceData property value. Composed type representation for type SpecificDatesRecurrenceData
     * @param SpecificDatesRecurrenceData|null $value Value to set for the SpecificDatesRecurrenceData property.
    */
    public function setSpecificDatesRecurrenceData(?SpecificDatesRecurrenceData $value): void {
        $this->specificDatesRecurrenceData = $value;
    }

}
