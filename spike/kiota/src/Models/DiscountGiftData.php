<?php

namespace Puntjes\Spike\Kiota\Models;

use Microsoft\Kiota\Abstractions\Serialization\AdditionalDataHolder;
use Microsoft\Kiota\Abstractions\Serialization\Parsable;
use Microsoft\Kiota\Abstractions\Serialization\ParseNode;
use Microsoft\Kiota\Abstractions\Serialization\SerializationWriter;

class DiscountGiftData implements AdditionalDataHolder, Parsable 
{
    /**
     * @var array<string, mixed>|null $additionalData Stores additional data not described in the OpenAPI description found when deserializing. Can be used for serialization as well.
    */
    private ?array $additionalData = null;
    
    /**
     * @var DiscountGiftData_discount|null $discount How much comes off. Its `type` says which shape this is.
    */
    private ?DiscountGiftData_discount $discount = null;
    
    /**
     * @var string|null $message A line the customer reads with the gift.
    */
    private ?string $message = null;
    
    /**
     * @var DiscountGiftData_type|null $type Always `discount` for this shape.
    */
    private ?DiscountGiftData_type $type = null;
    
    /**
     * @var int|null $validity_days How many days the customer has to use it.
    */
    private ?int $validity_days = null;
    
    /**
     * Instantiates a new DiscountGiftData and sets the default values.
    */
    public function __construct() {
        $this->setAdditionalData([]);
    }

    /**
     * Creates a new instance of the appropriate class based on discriminator value
     * @param ParseNode $parseNode The parse node to use to read the discriminator value and create the object
     * @return DiscountGiftData
    */
    public static function createFromDiscriminatorValue(ParseNode $parseNode): DiscountGiftData {
        return new DiscountGiftData();
    }

    /**
     * Gets the AdditionalData property value. Stores additional data not described in the OpenAPI description found when deserializing. Can be used for serialization as well.
     * @return array<string, mixed>|null
    */
    public function getAdditionalData(): ?array {
        return $this->additionalData;
    }

    /**
     * Gets the discount property value. How much comes off. Its `type` says which shape this is.
     * @return DiscountGiftData_discount|null
    */
    public function getDiscount(): ?DiscountGiftData_discount {
        return $this->discount;
    }

    /**
     * The deserialization information for the current model
     * @return array<string, callable(ParseNode): void>
    */
    public function getFieldDeserializers(): array {
        $o = $this;
        return  [
            'discount' => fn(ParseNode $n) => $o->setDiscount($n->getObjectValue([DiscountGiftData_discount::class, 'createFromDiscriminatorValue'])),
            'message' => fn(ParseNode $n) => $o->setMessage($n->getStringValue()),
            'type' => fn(ParseNode $n) => $o->setType($n->getEnumValue(DiscountGiftData_type::class)),
            'validity_days' => fn(ParseNode $n) => $o->setValidityDays($n->getIntegerValue()),
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
     * Gets the type property value. Always `discount` for this shape.
     * @return DiscountGiftData_type|null
    */
    public function getType(): ?DiscountGiftData_type {
        return $this->type;
    }

    /**
     * Gets the validity_days property value. How many days the customer has to use it.
     * @return int|null
    */
    public function getValidityDays(): ?int {
        return $this->validity_days;
    }

    /**
     * Serializes information the current object
     * @param SerializationWriter $writer Serialization writer to use to serialize this model
    */
    public function serialize(SerializationWriter $writer): void {
        $writer->writeObjectValue('discount', $this->getDiscount());
        $writer->writeStringValue('message', $this->getMessage());
        $writer->writeEnumValue('type', $this->getType());
        $writer->writeIntegerValue('validity_days', $this->getValidityDays());
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
     * Sets the discount property value. How much comes off. Its `type` says which shape this is.
     * @param DiscountGiftData_discount|null $value Value to set for the discount property.
    */
    public function setDiscount(?DiscountGiftData_discount $value): void {
        $this->discount = $value;
    }

    /**
     * Sets the message property value. A line the customer reads with the gift.
     * @param string|null $value Value to set for the message property.
    */
    public function setMessage(?string $value): void {
        $this->message = $value;
    }

    /**
     * Sets the type property value. Always `discount` for this shape.
     * @param DiscountGiftData_type|null $value Value to set for the type property.
    */
    public function setType(?DiscountGiftData_type $value): void {
        $this->type = $value;
    }

    /**
     * Sets the validity_days property value. How many days the customer has to use it.
     * @param int|null $value Value to set for the validity_days property.
    */
    public function setValidityDays(?int $value): void {
        $this->validity_days = $value;
    }

}
