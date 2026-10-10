<?php

namespace Puntjes\Spike\Kiota\Models;

use Microsoft\Kiota\Abstractions\Serialization\AdditionalDataHolder;
use Microsoft\Kiota\Abstractions\Serialization\Parsable;
use Microsoft\Kiota\Abstractions\Serialization\ParseNode;
use Microsoft\Kiota\Abstractions\Serialization\SerializationWriter;

class DateRangeRecurrenceData implements AdditionalDataHolder, Parsable 
{
    /**
     * @var string|null $active_from The first day it runs, as `MM-DD`.
    */
    private ?string $active_from = null;
    
    /**
     * @var string|null $active_until The last day it runs, as `MM-DD`. Earlier than `active_from` wraps over new year.
    */
    private ?string $active_until = null;
    
    /**
     * @var array<string, mixed>|null $additionalData Stores additional data not described in the OpenAPI description found when deserializing. Can be used for serialization as well.
    */
    private ?array $additionalData = null;
    
    /**
     * @var DateRangeRecurrenceData_type|null $type Which shape this is.
    */
    private ?DateRangeRecurrenceData_type $type = null;
    
    /**
     * Instantiates a new DateRangeRecurrenceData and sets the default values.
    */
    public function __construct() {
        $this->setAdditionalData([]);
    }

    /**
     * Creates a new instance of the appropriate class based on discriminator value
     * @param ParseNode $parseNode The parse node to use to read the discriminator value and create the object
     * @return DateRangeRecurrenceData
    */
    public static function createFromDiscriminatorValue(ParseNode $parseNode): DateRangeRecurrenceData {
        return new DateRangeRecurrenceData();
    }

    /**
     * Gets the active_from property value. The first day it runs, as `MM-DD`.
     * @return string|null
    */
    public function getActiveFrom(): ?string {
        return $this->active_from;
    }

    /**
     * Gets the active_until property value. The last day it runs, as `MM-DD`. Earlier than `active_from` wraps over new year.
     * @return string|null
    */
    public function getActiveUntil(): ?string {
        return $this->active_until;
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
            'active_from' => fn(ParseNode $n) => $o->setActiveFrom($n->getStringValue()),
            'active_until' => fn(ParseNode $n) => $o->setActiveUntil($n->getStringValue()),
            'type' => fn(ParseNode $n) => $o->setType($n->getEnumValue(DateRangeRecurrenceData_type::class)),
        ];
    }

    /**
     * Gets the type property value. Which shape this is.
     * @return DateRangeRecurrenceData_type|null
    */
    public function getType(): ?DateRangeRecurrenceData_type {
        return $this->type;
    }

    /**
     * Serializes information the current object
     * @param SerializationWriter $writer Serialization writer to use to serialize this model
    */
    public function serialize(SerializationWriter $writer): void {
        $writer->writeStringValue('active_from', $this->getActiveFrom());
        $writer->writeStringValue('active_until', $this->getActiveUntil());
        $writer->writeEnumValue('type', $this->getType());
        $writer->writeAdditionalData($this->getAdditionalData());
    }

    /**
     * Sets the active_from property value. The first day it runs, as `MM-DD`.
     * @param string|null $value Value to set for the active_from property.
    */
    public function setActiveFrom(?string $value): void {
        $this->active_from = $value;
    }

    /**
     * Sets the active_until property value. The last day it runs, as `MM-DD`. Earlier than `active_from` wraps over new year.
     * @param string|null $value Value to set for the active_until property.
    */
    public function setActiveUntil(?string $value): void {
        $this->active_until = $value;
    }

    /**
     * Sets the AdditionalData property value. Stores additional data not described in the OpenAPI description found when deserializing. Can be used for serialization as well.
     * @param array<string,mixed> $value Value to set for the AdditionalData property.
    */
    public function setAdditionalData(?array $value): void {
        $this->additionalData = $value;
    }

    /**
     * Sets the type property value. Which shape this is.
     * @param DateRangeRecurrenceData_type|null $value Value to set for the type property.
    */
    public function setType(?DateRangeRecurrenceData_type $value): void {
        $this->type = $value;
    }

}
