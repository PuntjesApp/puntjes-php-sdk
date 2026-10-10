<?php

namespace Puntjes\Spike\Kiota\Models;

use Microsoft\Kiota\Abstractions\Serialization\AdditionalDataHolder;
use Microsoft\Kiota\Abstractions\Serialization\Parsable;
use Microsoft\Kiota\Abstractions\Serialization\ParseNode;
use Microsoft\Kiota\Abstractions\Serialization\SerializationWriter;

class TransactionItemData implements AdditionalDataHolder, Parsable 
{
    /**
     * @var array<string, mixed>|null $additionalData Stores additional data not described in the OpenAPI description found when deserializing. Can be used for serialization as well.
    */
    private ?array $additionalData = null;
    
    /**
     * @var string|null $category The category property
    */
    private ?string $category = null;
    
    /**
     * @var int|null $id The id property
    */
    private ?int $id = null;
    
    /**
     * @var int|null $line_total The line_total property
    */
    private ?int $line_total = null;
    
    /**
     * @var string|null $name The name property
    */
    private ?string $name = null;
    
    /**
     * @var int|null $quantity The quantity property
    */
    private ?int $quantity = null;
    
    /**
     * @var string|null $sku The sku property
    */
    private ?string $sku = null;
    
    /**
     * @var int|null $unit_price The unit_price property
    */
    private ?int $unit_price = null;
    
    /**
     * Instantiates a new TransactionItemData and sets the default values.
    */
    public function __construct() {
        $this->setAdditionalData([]);
    }

    /**
     * Creates a new instance of the appropriate class based on discriminator value
     * @param ParseNode $parseNode The parse node to use to read the discriminator value and create the object
     * @return TransactionItemData
    */
    public static function createFromDiscriminatorValue(ParseNode $parseNode): TransactionItemData {
        return new TransactionItemData();
    }

    /**
     * Gets the AdditionalData property value. Stores additional data not described in the OpenAPI description found when deserializing. Can be used for serialization as well.
     * @return array<string, mixed>|null
    */
    public function getAdditionalData(): ?array {
        return $this->additionalData;
    }

    /**
     * Gets the category property value. The category property
     * @return string|null
    */
    public function getCategory(): ?string {
        return $this->category;
    }

    /**
     * The deserialization information for the current model
     * @return array<string, callable(ParseNode): void>
    */
    public function getFieldDeserializers(): array {
        $o = $this;
        return  [
            'category' => fn(ParseNode $n) => $o->setCategory($n->getStringValue()),
            'id' => fn(ParseNode $n) => $o->setId($n->getIntegerValue()),
            'line_total' => fn(ParseNode $n) => $o->setLineTotal($n->getIntegerValue()),
            'name' => fn(ParseNode $n) => $o->setName($n->getStringValue()),
            'quantity' => fn(ParseNode $n) => $o->setQuantity($n->getIntegerValue()),
            'sku' => fn(ParseNode $n) => $o->setSku($n->getStringValue()),
            'unit_price' => fn(ParseNode $n) => $o->setUnitPrice($n->getIntegerValue()),
        ];
    }

    /**
     * Gets the id property value. The id property
     * @return int|null
    */
    public function getId(): ?int {
        return $this->id;
    }

    /**
     * Gets the line_total property value. The line_total property
     * @return int|null
    */
    public function getLineTotal(): ?int {
        return $this->line_total;
    }

    /**
     * Gets the name property value. The name property
     * @return string|null
    */
    public function getName(): ?string {
        return $this->name;
    }

    /**
     * Gets the quantity property value. The quantity property
     * @return int|null
    */
    public function getQuantity(): ?int {
        return $this->quantity;
    }

    /**
     * Gets the sku property value. The sku property
     * @return string|null
    */
    public function getSku(): ?string {
        return $this->sku;
    }

    /**
     * Gets the unit_price property value. The unit_price property
     * @return int|null
    */
    public function getUnitPrice(): ?int {
        return $this->unit_price;
    }

    /**
     * Serializes information the current object
     * @param SerializationWriter $writer Serialization writer to use to serialize this model
    */
    public function serialize(SerializationWriter $writer): void {
        $writer->writeStringValue('category', $this->getCategory());
        $writer->writeIntegerValue('id', $this->getId());
        $writer->writeIntegerValue('line_total', $this->getLineTotal());
        $writer->writeStringValue('name', $this->getName());
        $writer->writeIntegerValue('quantity', $this->getQuantity());
        $writer->writeStringValue('sku', $this->getSku());
        $writer->writeIntegerValue('unit_price', $this->getUnitPrice());
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
     * Sets the category property value. The category property
     * @param string|null $value Value to set for the category property.
    */
    public function setCategory(?string $value): void {
        $this->category = $value;
    }

    /**
     * Sets the id property value. The id property
     * @param int|null $value Value to set for the id property.
    */
    public function setId(?int $value): void {
        $this->id = $value;
    }

    /**
     * Sets the line_total property value. The line_total property
     * @param int|null $value Value to set for the line_total property.
    */
    public function setLineTotal(?int $value): void {
        $this->line_total = $value;
    }

    /**
     * Sets the name property value. The name property
     * @param string|null $value Value to set for the name property.
    */
    public function setName(?string $value): void {
        $this->name = $value;
    }

    /**
     * Sets the quantity property value. The quantity property
     * @param int|null $value Value to set for the quantity property.
    */
    public function setQuantity(?int $value): void {
        $this->quantity = $value;
    }

    /**
     * Sets the sku property value. The sku property
     * @param string|null $value Value to set for the sku property.
    */
    public function setSku(?string $value): void {
        $this->sku = $value;
    }

    /**
     * Sets the unit_price property value. The unit_price property
     * @param int|null $value Value to set for the unit_price property.
    */
    public function setUnitPrice(?int $value): void {
        $this->unit_price = $value;
    }

}
