<?php

namespace Puntjes\Generated\Models;

use Microsoft\Kiota\Abstractions\Serialization\AdditionalDataHolder;
use Microsoft\Kiota\Abstractions\Serialization\Parsable;
use Microsoft\Kiota\Abstractions\Serialization\ParseNode;
use Microsoft\Kiota\Abstractions\Serialization\SerializationWriter;

class PartnerApiClientData implements AdditionalDataHolder, Parsable 
{
    /**
     * @var array<string, mixed>|null $additionalData Stores additional data not described in the OpenAPI description found when deserializing. Can be used for serialization as well.
    */
    private ?array $additionalData = null;
    
    /**
     * @var BranchReferenceData|null $branch The branch property
    */
    private ?BranchReferenceData $branch = null;
    
    /**
     * @var string|null $client_id The `client_id` for `POST /oauth/token`.
    */
    private ?string $client_id = null;
    
    /**
     * @var string|null $client_secret The `client_secret` for `POST /oauth/token`.
    */
    private ?string $client_secret = null;
    
    /**
     * @var PartnerApiClientData_vendor|null $vendor The vendor that allowed the connection.
    */
    private ?PartnerApiClientData_vendor $vendor = null;
    
    /**
     * Instantiates a new PartnerApiClientData and sets the default values.
    */
    public function __construct() {
        $this->setAdditionalData([]);
    }

    /**
     * Creates a new instance of the appropriate class based on discriminator value
     * @param ParseNode $parseNode The parse node to use to read the discriminator value and create the object
     * @return PartnerApiClientData
    */
    public static function createFromDiscriminatorValue(ParseNode $parseNode): PartnerApiClientData {
        return new PartnerApiClientData();
    }

    /**
     * Gets the AdditionalData property value. Stores additional data not described in the OpenAPI description found when deserializing. Can be used for serialization as well.
     * @return array<string, mixed>|null
    */
    public function getAdditionalData(): ?array {
        return $this->additionalData;
    }

    /**
     * Gets the branch property value. The branch property
     * @return BranchReferenceData|null
    */
    public function getBranch(): ?BranchReferenceData {
        return $this->branch;
    }

    /**
     * Gets the client_id property value. The `client_id` for `POST /oauth/token`.
     * @return string|null
    */
    public function getClientId(): ?string {
        return $this->client_id;
    }

    /**
     * Gets the client_secret property value. The `client_secret` for `POST /oauth/token`.
     * @return string|null
    */
    public function getClientSecret(): ?string {
        return $this->client_secret;
    }

    /**
     * The deserialization information for the current model
     * @return array<string, callable(ParseNode): void>
    */
    public function getFieldDeserializers(): array {
        $o = $this;
        return  [
            'branch' => fn(ParseNode $n) => $o->setBranch($n->getObjectValue([BranchReferenceData::class, 'createFromDiscriminatorValue'])),
            'client_id' => fn(ParseNode $n) => $o->setClientId($n->getStringValue()),
            'client_secret' => fn(ParseNode $n) => $o->setClientSecret($n->getStringValue()),
            'vendor' => fn(ParseNode $n) => $o->setVendor($n->getObjectValue([PartnerApiClientData_vendor::class, 'createFromDiscriminatorValue'])),
        ];
    }

    /**
     * Gets the vendor property value. The vendor that allowed the connection.
     * @return PartnerApiClientData_vendor|null
    */
    public function getVendor(): ?PartnerApiClientData_vendor {
        return $this->vendor;
    }

    /**
     * Serializes information the current object
     * @param SerializationWriter $writer Serialization writer to use to serialize this model
    */
    public function serialize(SerializationWriter $writer): void {
        $writer->writeObjectValue('branch', $this->getBranch());
        $writer->writeStringValue('client_id', $this->getClientId());
        $writer->writeStringValue('client_secret', $this->getClientSecret());
        $writer->writeObjectValue('vendor', $this->getVendor());
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
     * Sets the branch property value. The branch property
     * @param BranchReferenceData|null $value Value to set for the branch property.
    */
    public function setBranch(?BranchReferenceData $value): void {
        $this->branch = $value;
    }

    /**
     * Sets the client_id property value. The `client_id` for `POST /oauth/token`.
     * @param string|null $value Value to set for the client_id property.
    */
    public function setClientId(?string $value): void {
        $this->client_id = $value;
    }

    /**
     * Sets the client_secret property value. The `client_secret` for `POST /oauth/token`.
     * @param string|null $value Value to set for the client_secret property.
    */
    public function setClientSecret(?string $value): void {
        $this->client_secret = $value;
    }

    /**
     * Sets the vendor property value. The vendor that allowed the connection.
     * @param PartnerApiClientData_vendor|null $value Value to set for the vendor property.
    */
    public function setVendor(?PartnerApiClientData_vendor $value): void {
        $this->vendor = $value;
    }

}
