<?php

namespace Puntjes\Spike\Kiota\Models;

use Microsoft\Kiota\Abstractions\Serialization\AdditionalDataHolder;
use Microsoft\Kiota\Abstractions\Serialization\Parsable;
use Microsoft\Kiota\Abstractions\Serialization\ParseNode;
use Microsoft\Kiota\Abstractions\Serialization\SerializationWriter;

class StatisticsPeriodData implements AdditionalDataHolder, Parsable 
{
    /**
     * @var array<string, mixed>|null $additionalData Stores additional data not described in the OpenAPI description found when deserializing. Can be used for serialization as well.
    */
    private ?array $additionalData = null;
    
    /**
     * @var string|null $from The from property
    */
    private ?string $from = null;
    
    /**
     * @var string|null $granularity The granularity property
    */
    private ?string $granularity = null;
    
    /**
     * @var string|null $preset The preset property
    */
    private ?string $preset = null;
    
    /**
     * @var string|null $timezone The timezone property
    */
    private ?string $timezone = null;
    
    /**
     * @var string|null $to The to property
    */
    private ?string $to = null;
    
    /**
     * Instantiates a new StatisticsPeriodData and sets the default values.
    */
    public function __construct() {
        $this->setAdditionalData([]);
    }

    /**
     * Creates a new instance of the appropriate class based on discriminator value
     * @param ParseNode $parseNode The parse node to use to read the discriminator value and create the object
     * @return StatisticsPeriodData
    */
    public static function createFromDiscriminatorValue(ParseNode $parseNode): StatisticsPeriodData {
        return new StatisticsPeriodData();
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
            'from' => fn(ParseNode $n) => $o->setFrom($n->getStringValue()),
            'granularity' => fn(ParseNode $n) => $o->setGranularity($n->getStringValue()),
            'preset' => fn(ParseNode $n) => $o->setPreset($n->getStringValue()),
            'timezone' => fn(ParseNode $n) => $o->setTimezone($n->getStringValue()),
            'to' => fn(ParseNode $n) => $o->setTo($n->getStringValue()),
        ];
    }

    /**
     * Gets the from property value. The from property
     * @return string|null
    */
    public function getFrom(): ?string {
        return $this->from;
    }

    /**
     * Gets the granularity property value. The granularity property
     * @return string|null
    */
    public function getGranularity(): ?string {
        return $this->granularity;
    }

    /**
     * Gets the preset property value. The preset property
     * @return string|null
    */
    public function getPreset(): ?string {
        return $this->preset;
    }

    /**
     * Gets the timezone property value. The timezone property
     * @return string|null
    */
    public function getTimezone(): ?string {
        return $this->timezone;
    }

    /**
     * Gets the to property value. The to property
     * @return string|null
    */
    public function getTo(): ?string {
        return $this->to;
    }

    /**
     * Serializes information the current object
     * @param SerializationWriter $writer Serialization writer to use to serialize this model
    */
    public function serialize(SerializationWriter $writer): void {
        $writer->writeStringValue('from', $this->getFrom());
        $writer->writeStringValue('granularity', $this->getGranularity());
        $writer->writeStringValue('preset', $this->getPreset());
        $writer->writeStringValue('timezone', $this->getTimezone());
        $writer->writeStringValue('to', $this->getTo());
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
     * Sets the from property value. The from property
     * @param string|null $value Value to set for the from property.
    */
    public function setFrom(?string $value): void {
        $this->from = $value;
    }

    /**
     * Sets the granularity property value. The granularity property
     * @param string|null $value Value to set for the granularity property.
    */
    public function setGranularity(?string $value): void {
        $this->granularity = $value;
    }

    /**
     * Sets the preset property value. The preset property
     * @param string|null $value Value to set for the preset property.
    */
    public function setPreset(?string $value): void {
        $this->preset = $value;
    }

    /**
     * Sets the timezone property value. The timezone property
     * @param string|null $value Value to set for the timezone property.
    */
    public function setTimezone(?string $value): void {
        $this->timezone = $value;
    }

    /**
     * Sets the to property value. The to property
     * @param string|null $value Value to set for the to property.
    */
    public function setTo(?string $value): void {
        $this->to = $value;
    }

}
