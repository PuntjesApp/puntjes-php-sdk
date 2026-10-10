<?php

namespace Puntjes\Spike\Kiota\Models;

use Microsoft\Kiota\Abstractions\Serialization\AdditionalDataHolder;
use Microsoft\Kiota\Abstractions\Serialization\Parsable;
use Microsoft\Kiota\Abstractions\Serialization\ParseNode;
use Microsoft\Kiota\Abstractions\Serialization\SerializationWriter;
use Microsoft\Kiota\Abstractions\Types\TypeUtils;

class DaysOfWeekRecurrenceData implements AdditionalDataHolder, Parsable 
{
    /**
     * @var array<string, mixed>|null $additionalData Stores additional data not described in the OpenAPI description found when deserializing. Can be used for serialization as well.
    */
    private ?array $additionalData = null;
    
    /**
     * @var array<int>|null $days The days it runs, 0 for Sunday through 6 for Saturday.
    */
    private ?array $days = null;
    
    /**
     * @var DaysOfWeekRecurrenceData_type|null $type Which shape this is.
    */
    private ?DaysOfWeekRecurrenceData_type $type = null;
    
    /**
     * Instantiates a new DaysOfWeekRecurrenceData and sets the default values.
    */
    public function __construct() {
        $this->setAdditionalData([]);
    }

    /**
     * Creates a new instance of the appropriate class based on discriminator value
     * @param ParseNode $parseNode The parse node to use to read the discriminator value and create the object
     * @return DaysOfWeekRecurrenceData
    */
    public static function createFromDiscriminatorValue(ParseNode $parseNode): DaysOfWeekRecurrenceData {
        return new DaysOfWeekRecurrenceData();
    }

    /**
     * Gets the AdditionalData property value. Stores additional data not described in the OpenAPI description found when deserializing. Can be used for serialization as well.
     * @return array<string, mixed>|null
    */
    public function getAdditionalData(): ?array {
        return $this->additionalData;
    }

    /**
     * Gets the days property value. The days it runs, 0 for Sunday through 6 for Saturday.
     * @return array<int>|null
    */
    public function getDays(): ?array {
        return $this->days;
    }

    /**
     * The deserialization information for the current model
     * @return array<string, callable(ParseNode): void>
    */
    public function getFieldDeserializers(): array {
        $o = $this;
        return  [
            'days' => function (ParseNode $n) {
                $val = $n->getCollectionOfPrimitiveValues();
                if (is_array($val)) {
                    TypeUtils::validateCollectionValues($val, 'int');
                }
                /** @var array<int>|null $val */
                $this->setDays($val);
            },
            'type' => fn(ParseNode $n) => $o->setType($n->getEnumValue(DaysOfWeekRecurrenceData_type::class)),
        ];
    }

    /**
     * Gets the type property value. Which shape this is.
     * @return DaysOfWeekRecurrenceData_type|null
    */
    public function getType(): ?DaysOfWeekRecurrenceData_type {
        return $this->type;
    }

    /**
     * Serializes information the current object
     * @param SerializationWriter $writer Serialization writer to use to serialize this model
    */
    public function serialize(SerializationWriter $writer): void {
        $writer->writeCollectionOfPrimitiveValues('days', $this->getDays());
        $writer->writeEnumValue('type', $this->getType());
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
     * Sets the days property value. The days it runs, 0 for Sunday through 6 for Saturday.
     * @param array<int>|null $value Value to set for the days property.
    */
    public function setDays(?array $value): void {
        $this->days = $value;
    }

    /**
     * Sets the type property value. Which shape this is.
     * @param DaysOfWeekRecurrenceData_type|null $value Value to set for the type property.
    */
    public function setType(?DaysOfWeekRecurrenceData_type $value): void {
        $this->type = $value;
    }

}
