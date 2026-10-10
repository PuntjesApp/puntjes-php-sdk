<?php

namespace Puntjes\Spike\Kiota\Models;

use Microsoft\Kiota\Abstractions\Serialization\AdditionalDataHolder;
use Microsoft\Kiota\Abstractions\Serialization\Parsable;
use Microsoft\Kiota\Abstractions\Serialization\ParseNode;
use Microsoft\Kiota\Abstractions\Serialization\SerializationWriter;

class CreateTransactionData implements AdditionalDataHolder, Parsable 
{
    /**
     * @var array<string, mixed>|null $additionalData Stores additional data not described in the OpenAPI description found when deserializing. Can be used for serialization as well.
    */
    private ?array $additionalData = null;
    
    /**
     * @var string|null $branch The branch property
    */
    private ?string $branch = null;
    
    /**
     * @var string|null $description The description property
    */
    private ?string $description = null;
    
    /**
     * @var string|null $external_reference The external_reference property
    */
    private ?string $external_reference = null;
    
    /**
     * @var string|null $idempotency_key The idempotency_key property
    */
    private ?string $idempotency_key = null;
    
    /**
     * @var string|null $identifier The identifier property
    */
    private ?string $identifier = null;
    
    /**
     * @var array<CreateTransactionItemData>|null $items Optional order line items.
    */
    private ?array $items = null;
    
    /**
     * @var int|null $total_amount The total_amount property
    */
    private ?int $total_amount = null;
    
    /**
     * Instantiates a new CreateTransactionData and sets the default values.
    */
    public function __construct() {
        $this->setAdditionalData([]);
    }

    /**
     * Creates a new instance of the appropriate class based on discriminator value
     * @param ParseNode $parseNode The parse node to use to read the discriminator value and create the object
     * @return CreateTransactionData
    */
    public static function createFromDiscriminatorValue(ParseNode $parseNode): CreateTransactionData {
        return new CreateTransactionData();
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
     * @return string|null
    */
    public function getBranch(): ?string {
        return $this->branch;
    }

    /**
     * Gets the description property value. The description property
     * @return string|null
    */
    public function getDescription(): ?string {
        return $this->description;
    }

    /**
     * Gets the external_reference property value. The external_reference property
     * @return string|null
    */
    public function getExternalReference(): ?string {
        return $this->external_reference;
    }

    /**
     * The deserialization information for the current model
     * @return array<string, callable(ParseNode): void>
    */
    public function getFieldDeserializers(): array {
        $o = $this;
        return  [
            'branch' => fn(ParseNode $n) => $o->setBranch($n->getStringValue()),
            'description' => fn(ParseNode $n) => $o->setDescription($n->getStringValue()),
            'external_reference' => fn(ParseNode $n) => $o->setExternalReference($n->getStringValue()),
            'idempotency_key' => fn(ParseNode $n) => $o->setIdempotencyKey($n->getStringValue()),
            'identifier' => fn(ParseNode $n) => $o->setIdentifier($n->getStringValue()),
            'items' => fn(ParseNode $n) => $o->setItems($n->getCollectionOfObjectValues([CreateTransactionItemData::class, 'createFromDiscriminatorValue'])),
            'total_amount' => fn(ParseNode $n) => $o->setTotalAmount($n->getIntegerValue()),
        ];
    }

    /**
     * Gets the idempotency_key property value. The idempotency_key property
     * @return string|null
    */
    public function getIdempotencyKey(): ?string {
        return $this->idempotency_key;
    }

    /**
     * Gets the identifier property value. The identifier property
     * @return string|null
    */
    public function getIdentifier(): ?string {
        return $this->identifier;
    }

    /**
     * Gets the items property value. Optional order line items.
     * @return array<CreateTransactionItemData>|null
    */
    public function getItems(): ?array {
        return $this->items;
    }

    /**
     * Gets the total_amount property value. The total_amount property
     * @return int|null
    */
    public function getTotalAmount(): ?int {
        return $this->total_amount;
    }

    /**
     * Serializes information the current object
     * @param SerializationWriter $writer Serialization writer to use to serialize this model
    */
    public function serialize(SerializationWriter $writer): void {
        $writer->writeStringValue('branch', $this->getBranch());
        $writer->writeStringValue('description', $this->getDescription());
        $writer->writeStringValue('external_reference', $this->getExternalReference());
        $writer->writeStringValue('idempotency_key', $this->getIdempotencyKey());
        $writer->writeStringValue('identifier', $this->getIdentifier());
        $writer->writeCollectionOfObjectValues('items', $this->getItems());
        $writer->writeIntegerValue('total_amount', $this->getTotalAmount());
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
     * @param string|null $value Value to set for the branch property.
    */
    public function setBranch(?string $value): void {
        $this->branch = $value;
    }

    /**
     * Sets the description property value. The description property
     * @param string|null $value Value to set for the description property.
    */
    public function setDescription(?string $value): void {
        $this->description = $value;
    }

    /**
     * Sets the external_reference property value. The external_reference property
     * @param string|null $value Value to set for the external_reference property.
    */
    public function setExternalReference(?string $value): void {
        $this->external_reference = $value;
    }

    /**
     * Sets the idempotency_key property value. The idempotency_key property
     * @param string|null $value Value to set for the idempotency_key property.
    */
    public function setIdempotencyKey(?string $value): void {
        $this->idempotency_key = $value;
    }

    /**
     * Sets the identifier property value. The identifier property
     * @param string|null $value Value to set for the identifier property.
    */
    public function setIdentifier(?string $value): void {
        $this->identifier = $value;
    }

    /**
     * Sets the items property value. Optional order line items.
     * @param array<CreateTransactionItemData>|null $value Value to set for the items property.
    */
    public function setItems(?array $value): void {
        $this->items = $value;
    }

    /**
     * Sets the total_amount property value. The total_amount property
     * @param int|null $value Value to set for the total_amount property.
    */
    public function setTotalAmount(?int $value): void {
        $this->total_amount = $value;
    }

}
