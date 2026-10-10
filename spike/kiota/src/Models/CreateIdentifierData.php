<?php

namespace Puntjes\Spike\Kiota\Models;

use Microsoft\Kiota\Abstractions\Serialization\AdditionalDataHolder;
use Microsoft\Kiota\Abstractions\Serialization\Parsable;
use Microsoft\Kiota\Abstractions\Serialization\ParseNode;
use Microsoft\Kiota\Abstractions\Serialization\SerializationWriter;

class CreateIdentifierData implements AdditionalDataHolder, Parsable 
{
    /**
     * @var array<string, mixed>|null $additionalData Stores additional data not described in the OpenAPI description found when deserializing. Can be used for serialization as well.
    */
    private ?array $additionalData = null;
    
    /**
     * @var bool|null $is_primary The is_primary property
    */
    private ?bool $is_primary = null;
    
    /**
     * @var CreateIdentifierData_type|null $type The type property
    */
    private ?CreateIdentifierData_type $type = null;
    
    /**
     * @var string|null $value The value property
    */
    private ?string $value = null;
    
    /**
     * Instantiates a new CreateIdentifierData and sets the default values.
    */
    public function __construct() {
        $this->setAdditionalData([]);
    }

    /**
     * Creates a new instance of the appropriate class based on discriminator value
     * @param ParseNode $parseNode The parse node to use to read the discriminator value and create the object
     * @return CreateIdentifierData
    */
    public static function createFromDiscriminatorValue(ParseNode $parseNode): CreateIdentifierData {
        return new CreateIdentifierData();
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
            'is_primary' => fn(ParseNode $n) => $o->setIsPrimary($n->getBooleanValue()),
            'type' => fn(ParseNode $n) => $o->setType($n->getEnumValue(CreateIdentifierData_type::class)),
            'value' => fn(ParseNode $n) => $o->setValue($n->getStringValue()),
        ];
    }

    /**
     * Gets the is_primary property value. The is_primary property
     * @return bool|null
    */
    public function getIsPrimary(): ?bool {
        return $this->is_primary;
    }

    /**
     * Gets the type property value. The type property
     * @return CreateIdentifierData_type|null
    */
    public function getType(): ?CreateIdentifierData_type {
        return $this->type;
    }

    /**
     * Gets the value property value. The value property
     * @return string|null
    */
    public function getValue(): ?string {
        return $this->value;
    }

    /**
     * Serializes information the current object
     * @param SerializationWriter $writer Serialization writer to use to serialize this model
    */
    public function serialize(SerializationWriter $writer): void {
        $writer->writeBooleanValue('is_primary', $this->getIsPrimary());
        $writer->writeEnumValue('type', $this->getType());
        $writer->writeStringValue('value', $this->getValue());
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
     * Sets the is_primary property value. The is_primary property
     * @param bool|null $value Value to set for the is_primary property.
    */
    public function setIsPrimary(?bool $value): void {
        $this->is_primary = $value;
    }

    /**
     * Sets the type property value. The type property
     * @param CreateIdentifierData_type|null $value Value to set for the type property.
    */
    public function setType(?CreateIdentifierData_type $value): void {
        $this->type = $value;
    }

    /**
     * Sets the value property value. The value property
     * @param string|null $value Value to set for the value property.
    */
    public function setValue(?string $value): void {
        $this->value = $value;
    }

}
