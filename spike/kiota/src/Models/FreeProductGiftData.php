<?php

namespace Puntjes\Spike\Kiota\Models;

use Microsoft\Kiota\Abstractions\Serialization\AdditionalDataHolder;
use Microsoft\Kiota\Abstractions\Serialization\Parsable;
use Microsoft\Kiota\Abstractions\Serialization\ParseNode;
use Microsoft\Kiota\Abstractions\Serialization\SerializationWriter;

class FreeProductGiftData implements AdditionalDataHolder, Parsable 
{
    /**
     * @var array<string, mixed>|null $additionalData Stores additional data not described in the OpenAPI description found when deserializing. Can be used for serialization as well.
    */
    private ?array $additionalData = null;
    
    /**
     * @var string|null $message A line the customer reads with the gift.
    */
    private ?string $message = null;
    
    /**
     * @var array<GiftProductData>|null $products The products the customer gets.
    */
    private ?array $products = null;
    
    /**
     * @var FreeProductGiftData_type|null $type Always `free_product` for this shape.
    */
    private ?FreeProductGiftData_type $type = null;
    
    /**
     * Instantiates a new FreeProductGiftData and sets the default values.
    */
    public function __construct() {
        $this->setAdditionalData([]);
    }

    /**
     * Creates a new instance of the appropriate class based on discriminator value
     * @param ParseNode $parseNode The parse node to use to read the discriminator value and create the object
     * @return FreeProductGiftData
    */
    public static function createFromDiscriminatorValue(ParseNode $parseNode): FreeProductGiftData {
        return new FreeProductGiftData();
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
            'message' => fn(ParseNode $n) => $o->setMessage($n->getStringValue()),
            'products' => fn(ParseNode $n) => $o->setProducts($n->getCollectionOfObjectValues([GiftProductData::class, 'createFromDiscriminatorValue'])),
            'type' => fn(ParseNode $n) => $o->setType($n->getEnumValue(FreeProductGiftData_type::class)),
        ];
    }

    /**
     * Gets the message property value. A line the customer reads with the gift.
     * @return string|null
    */
    public function getMessage(): ?string {
        return $this->message;
    }

    /**
     * Gets the products property value. The products the customer gets.
     * @return array<GiftProductData>|null
    */
    public function getProducts(): ?array {
        return $this->products;
    }

    /**
     * Gets the type property value. Always `free_product` for this shape.
     * @return FreeProductGiftData_type|null
    */
    public function getType(): ?FreeProductGiftData_type {
        return $this->type;
    }

    /**
     * Serializes information the current object
     * @param SerializationWriter $writer Serialization writer to use to serialize this model
    */
    public function serialize(SerializationWriter $writer): void {
        $writer->writeStringValue('message', $this->getMessage());
        $writer->writeCollectionOfObjectValues('products', $this->getProducts());
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
     * Sets the message property value. A line the customer reads with the gift.
     * @param string|null $value Value to set for the message property.
    */
    public function setMessage(?string $value): void {
        $this->message = $value;
    }

    /**
     * Sets the products property value. The products the customer gets.
     * @param array<GiftProductData>|null $value Value to set for the products property.
    */
    public function setProducts(?array $value): void {
        $this->products = $value;
    }

    /**
     * Sets the type property value. Always `free_product` for this shape.
     * @param FreeProductGiftData_type|null $value Value to set for the type property.
    */
    public function setType(?FreeProductGiftData_type $value): void {
        $this->type = $value;
    }

}
