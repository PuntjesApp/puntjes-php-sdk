<?php

namespace Puntjes\Generated\Models;

use Microsoft\Kiota\Abstractions\Serialization\AdditionalDataHolder;
use Microsoft\Kiota\Abstractions\Serialization\Parsable;
use Microsoft\Kiota\Abstractions\Serialization\ParseNode;
use Microsoft\Kiota\Abstractions\Serialization\SerializationWriter;

class CreatePartnerApiClientData implements AdditionalDataHolder, Parsable 
{
    /**
     * @var array<string, mixed>|null $additionalData Stores additional data not described in the OpenAPI description found when deserializing. Can be used for serialization as well.
    */
    private ?array $additionalData = null;
    
    /**
     * @var string|null $connection The `connection` handle the partner got on its redirect URI after the admin allowed.
    */
    private ?string $connection = null;
    
    /**
     * @var string|null $label The name of the new client, as the vendor sees it under API clients.
    */
    private ?string $label = null;
    
    /**
     * Instantiates a new CreatePartnerApiClientData and sets the default values.
    */
    public function __construct() {
        $this->setAdditionalData([]);
    }

    /**
     * Creates a new instance of the appropriate class based on discriminator value
     * @param ParseNode $parseNode The parse node to use to read the discriminator value and create the object
     * @return CreatePartnerApiClientData
    */
    public static function createFromDiscriminatorValue(ParseNode $parseNode): CreatePartnerApiClientData {
        return new CreatePartnerApiClientData();
    }

    /**
     * Gets the AdditionalData property value. Stores additional data not described in the OpenAPI description found when deserializing. Can be used for serialization as well.
     * @return array<string, mixed>|null
    */
    public function getAdditionalData(): ?array {
        return $this->additionalData;
    }

    /**
     * Gets the connection property value. The `connection` handle the partner got on its redirect URI after the admin allowed.
     * @return string|null
    */
    public function getConnection(): ?string {
        return $this->connection;
    }

    /**
     * The deserialization information for the current model
     * @return array<string, callable(ParseNode): void>
    */
    public function getFieldDeserializers(): array {
        $o = $this;
        return  [
            'connection' => fn(ParseNode $n) => $o->setConnection($n->getStringValue()),
            'label' => fn(ParseNode $n) => $o->setLabel($n->getStringValue()),
        ];
    }

    /**
     * Gets the label property value. The name of the new client, as the vendor sees it under API clients.
     * @return string|null
    */
    public function getLabel(): ?string {
        return $this->label;
    }

    /**
     * Serializes information the current object
     * @param SerializationWriter $writer Serialization writer to use to serialize this model
    */
    public function serialize(SerializationWriter $writer): void {
        $writer->writeStringValue('connection', $this->getConnection());
        $writer->writeStringValue('label', $this->getLabel());
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
     * Sets the connection property value. The `connection` handle the partner got on its redirect URI after the admin allowed.
     * @param string|null $value Value to set for the connection property.
    */
    public function setConnection(?string $value): void {
        $this->connection = $value;
    }

    /**
     * Sets the label property value. The name of the new client, as the vendor sees it under API clients.
     * @param string|null $value Value to set for the label property.
    */
    public function setLabel(?string $value): void {
        $this->label = $value;
    }

}
