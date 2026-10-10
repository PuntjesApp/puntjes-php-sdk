<?php

namespace Puntjes\Spike\Kiota\Models;

use Microsoft\Kiota\Abstractions\Serialization\AdditionalDataHolder;
use Microsoft\Kiota\Abstractions\Serialization\Parsable;
use Microsoft\Kiota\Abstractions\Serialization\ParseNode;
use Microsoft\Kiota\Abstractions\Serialization\SerializationWriter;

class LinkExternalIdData implements AdditionalDataHolder, Parsable 
{
    /**
     * @var array<string, mixed>|null $additionalData Stores additional data not described in the OpenAPI description found when deserializing. Can be used for serialization as well.
    */
    private ?array $additionalData = null;
    
    /**
     * @var string|null $external_id The external_id property
    */
    private ?string $external_id = null;
    
    /**
     * @var string|null $identifier The identifier property
    */
    private ?string $identifier = null;
    
    /**
     * Instantiates a new LinkExternalIdData and sets the default values.
    */
    public function __construct() {
        $this->setAdditionalData([]);
    }

    /**
     * Creates a new instance of the appropriate class based on discriminator value
     * @param ParseNode $parseNode The parse node to use to read the discriminator value and create the object
     * @return LinkExternalIdData
    */
    public static function createFromDiscriminatorValue(ParseNode $parseNode): LinkExternalIdData {
        return new LinkExternalIdData();
    }

    /**
     * Gets the AdditionalData property value. Stores additional data not described in the OpenAPI description found when deserializing. Can be used for serialization as well.
     * @return array<string, mixed>|null
    */
    public function getAdditionalData(): ?array {
        return $this->additionalData;
    }

    /**
     * Gets the external_id property value. The external_id property
     * @return string|null
    */
    public function getExternalId(): ?string {
        return $this->external_id;
    }

    /**
     * The deserialization information for the current model
     * @return array<string, callable(ParseNode): void>
    */
    public function getFieldDeserializers(): array {
        $o = $this;
        return  [
            'external_id' => fn(ParseNode $n) => $o->setExternalId($n->getStringValue()),
            'identifier' => fn(ParseNode $n) => $o->setIdentifier($n->getStringValue()),
        ];
    }

    /**
     * Gets the identifier property value. The identifier property
     * @return string|null
    */
    public function getIdentifier(): ?string {
        return $this->identifier;
    }

    /**
     * Serializes information the current object
     * @param SerializationWriter $writer Serialization writer to use to serialize this model
    */
    public function serialize(SerializationWriter $writer): void {
        $writer->writeStringValue('external_id', $this->getExternalId());
        $writer->writeStringValue('identifier', $this->getIdentifier());
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
     * Sets the external_id property value. The external_id property
     * @param string|null $value Value to set for the external_id property.
    */
    public function setExternalId(?string $value): void {
        $this->external_id = $value;
    }

    /**
     * Sets the identifier property value. The identifier property
     * @param string|null $value Value to set for the identifier property.
    */
    public function setIdentifier(?string $value): void {
        $this->identifier = $value;
    }

}
