<?php

namespace Puntjes\Spike\Kiota\Models;

use Microsoft\Kiota\Abstractions\Serialization\AdditionalDataHolder;
use Microsoft\Kiota\Abstractions\Serialization\Parsable;
use Microsoft\Kiota\Abstractions\Serialization\ParseNode;
use Microsoft\Kiota\Abstractions\Serialization\SerializationWriter;
use Microsoft\Kiota\Abstractions\Types\TypeUtils;

class SpecificDatesRecurrenceData implements AdditionalDataHolder, Parsable 
{
    /**
     * @var array<string, mixed>|null $additionalData Stores additional data not described in the OpenAPI description found when deserializing. Can be used for serialization as well.
    */
    private ?array $additionalData = null;
    
    /**
     * @var array<string>|null $dates The days it runs, each as `YYYY-MM-DD`.
    */
    private ?array $dates = null;
    
    /**
     * @var SpecificDatesRecurrenceData_type|null $type Which shape this is.
    */
    private ?SpecificDatesRecurrenceData_type $type = null;
    
    /**
     * Instantiates a new SpecificDatesRecurrenceData and sets the default values.
    */
    public function __construct() {
        $this->setAdditionalData([]);
    }

    /**
     * Creates a new instance of the appropriate class based on discriminator value
     * @param ParseNode $parseNode The parse node to use to read the discriminator value and create the object
     * @return SpecificDatesRecurrenceData
    */
    public static function createFromDiscriminatorValue(ParseNode $parseNode): SpecificDatesRecurrenceData {
        return new SpecificDatesRecurrenceData();
    }

    /**
     * Gets the AdditionalData property value. Stores additional data not described in the OpenAPI description found when deserializing. Can be used for serialization as well.
     * @return array<string, mixed>|null
    */
    public function getAdditionalData(): ?array {
        return $this->additionalData;
    }

    /**
     * Gets the dates property value. The days it runs, each as `YYYY-MM-DD`.
     * @return array<string>|null
    */
    public function getDates(): ?array {
        return $this->dates;
    }

    /**
     * The deserialization information for the current model
     * @return array<string, callable(ParseNode): void>
    */
    public function getFieldDeserializers(): array {
        $o = $this;
        return  [
            'dates' => function (ParseNode $n) {
                $val = $n->getCollectionOfPrimitiveValues();
                if (is_array($val)) {
                    TypeUtils::validateCollectionValues($val, 'string');
                }
                /** @var array<string>|null $val */
                $this->setDates($val);
            },
            'type' => fn(ParseNode $n) => $o->setType($n->getEnumValue(SpecificDatesRecurrenceData_type::class)),
        ];
    }

    /**
     * Gets the type property value. Which shape this is.
     * @return SpecificDatesRecurrenceData_type|null
    */
    public function getType(): ?SpecificDatesRecurrenceData_type {
        return $this->type;
    }

    /**
     * Serializes information the current object
     * @param SerializationWriter $writer Serialization writer to use to serialize this model
    */
    public function serialize(SerializationWriter $writer): void {
        $writer->writeCollectionOfPrimitiveValues('dates', $this->getDates());
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
     * Sets the dates property value. The days it runs, each as `YYYY-MM-DD`.
     * @param array<string>|null $value Value to set for the dates property.
    */
    public function setDates(?array $value): void {
        $this->dates = $value;
    }

    /**
     * Sets the type property value. Which shape this is.
     * @param SpecificDatesRecurrenceData_type|null $value Value to set for the type property.
    */
    public function setType(?SpecificDatesRecurrenceData_type $value): void {
        $this->type = $value;
    }

}
