<?php

namespace Puntjes\Spike\Kiota\Models;

use Microsoft\Kiota\Abstractions\Serialization\AdditionalDataHolder;
use Microsoft\Kiota\Abstractions\Serialization\Parsable;
use Microsoft\Kiota\Abstractions\Serialization\ParseNode;
use Microsoft\Kiota\Abstractions\Serialization\SerializationWriter;

class VoucherGiftProductData implements AdditionalDataHolder, Parsable 
{
    /**
     * @var array<string, mixed>|null $additionalData Stores additional data not described in the OpenAPI description found when deserializing. Can be used for serialization as well.
    */
    private ?array $additionalData = null;
    
    /**
     * @var int|null $id The product this refers to, or null when the gift named no catalogue product.
    */
    private ?int $id = null;
    
    /**
     * @var string|null $name The product name as it was when the bon was issued.
    */
    private ?string $name = null;
    
    /**
     * @var string|null $product_reference The item number when the bon was issued, or null when it had none.
    */
    private ?string $product_reference = null;
    
    /**
     * @var int|null $quantity How many of this product the customer gets.
    */
    private ?int $quantity = null;
    
    /**
     * Instantiates a new VoucherGiftProductData and sets the default values.
    */
    public function __construct() {
        $this->setAdditionalData([]);
    }

    /**
     * Creates a new instance of the appropriate class based on discriminator value
     * @param ParseNode $parseNode The parse node to use to read the discriminator value and create the object
     * @return VoucherGiftProductData
    */
    public static function createFromDiscriminatorValue(ParseNode $parseNode): VoucherGiftProductData {
        return new VoucherGiftProductData();
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
            'id' => fn(ParseNode $n) => $o->setId($n->getIntegerValue()),
            'name' => fn(ParseNode $n) => $o->setName($n->getStringValue()),
            'product_reference' => fn(ParseNode $n) => $o->setProductReference($n->getStringValue()),
            'quantity' => fn(ParseNode $n) => $o->setQuantity($n->getIntegerValue()),
        ];
    }

    /**
     * Gets the id property value. The product this refers to, or null when the gift named no catalogue product.
     * @return int|null
    */
    public function getId(): ?int {
        return $this->id;
    }

    /**
     * Gets the name property value. The product name as it was when the bon was issued.
     * @return string|null
    */
    public function getName(): ?string {
        return $this->name;
    }

    /**
     * Gets the product_reference property value. The item number when the bon was issued, or null when it had none.
     * @return string|null
    */
    public function getProductReference(): ?string {
        return $this->product_reference;
    }

    /**
     * Gets the quantity property value. How many of this product the customer gets.
     * @return int|null
    */
    public function getQuantity(): ?int {
        return $this->quantity;
    }

    /**
     * Serializes information the current object
     * @param SerializationWriter $writer Serialization writer to use to serialize this model
    */
    public function serialize(SerializationWriter $writer): void {
        $writer->writeIntegerValue('id', $this->getId());
        $writer->writeStringValue('name', $this->getName());
        $writer->writeStringValue('product_reference', $this->getProductReference());
        $writer->writeIntegerValue('quantity', $this->getQuantity());
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
     * Sets the id property value. The product this refers to, or null when the gift named no catalogue product.
     * @param int|null $value Value to set for the id property.
    */
    public function setId(?int $value): void {
        $this->id = $value;
    }

    /**
     * Sets the name property value. The product name as it was when the bon was issued.
     * @param string|null $value Value to set for the name property.
    */
    public function setName(?string $value): void {
        $this->name = $value;
    }

    /**
     * Sets the product_reference property value. The item number when the bon was issued, or null when it had none.
     * @param string|null $value Value to set for the product_reference property.
    */
    public function setProductReference(?string $value): void {
        $this->product_reference = $value;
    }

    /**
     * Sets the quantity property value. How many of this product the customer gets.
     * @param int|null $value Value to set for the quantity property.
    */
    public function setQuantity(?int $value): void {
        $this->quantity = $value;
    }

}
