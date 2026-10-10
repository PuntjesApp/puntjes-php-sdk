<?php

namespace Puntjes\Spike\Kiota\Models;

use Microsoft\Kiota\Abstractions\Serialization\AdditionalDataHolder;
use Microsoft\Kiota\Abstractions\Serialization\Parsable;
use Microsoft\Kiota\Abstractions\Serialization\ParseNode;
use Microsoft\Kiota\Abstractions\Serialization\SerializationWriter;

class DiscountRedemptionData implements AdditionalDataHolder, Parsable 
{
    /**
     * @var array<string, mixed>|null $additionalData Stores additional data not described in the OpenAPI description found when deserializing. Can be used for serialization as well.
    */
    private ?array $additionalData = null;
    
    /**
     * @var DiscountRedemptionData_discount_type|null $discount_type Which of the two `discount_value` is.
    */
    private ?DiscountRedemptionData_discount_type $discount_type = null;
    
    /**
     * @var int|null $discount_value How much comes off: a percentage, or an amount in cents.
    */
    private ?int $discount_value = null;
    
    /**
     * @var string|null $product_reference The item number of the one product the discount is for; null when it counts on the whole purchase.
    */
    private ?string $product_reference = null;
    
    /**
     * @var DiscountRedemptionData_type|null $type Which shape this is.
    */
    private ?DiscountRedemptionData_type $type = null;
    
    /**
     * Instantiates a new DiscountRedemptionData and sets the default values.
    */
    public function __construct() {
        $this->setAdditionalData([]);
    }

    /**
     * Creates a new instance of the appropriate class based on discriminator value
     * @param ParseNode $parseNode The parse node to use to read the discriminator value and create the object
     * @return DiscountRedemptionData
    */
    public static function createFromDiscriminatorValue(ParseNode $parseNode): DiscountRedemptionData {
        return new DiscountRedemptionData();
    }

    /**
     * Gets the AdditionalData property value. Stores additional data not described in the OpenAPI description found when deserializing. Can be used for serialization as well.
     * @return array<string, mixed>|null
    */
    public function getAdditionalData(): ?array {
        return $this->additionalData;
    }

    /**
     * Gets the discount_type property value. Which of the two `discount_value` is.
     * @return DiscountRedemptionData_discount_type|null
    */
    public function getDiscountType(): ?DiscountRedemptionData_discount_type {
        return $this->discount_type;
    }

    /**
     * Gets the discount_value property value. How much comes off: a percentage, or an amount in cents.
     * @return int|null
    */
    public function getDiscountValue(): ?int {
        return $this->discount_value;
    }

    /**
     * The deserialization information for the current model
     * @return array<string, callable(ParseNode): void>
    */
    public function getFieldDeserializers(): array {
        $o = $this;
        return  [
            'discount_type' => fn(ParseNode $n) => $o->setDiscountType($n->getEnumValue(DiscountRedemptionData_discount_type::class)),
            'discount_value' => fn(ParseNode $n) => $o->setDiscountValue($n->getIntegerValue()),
            'product_reference' => fn(ParseNode $n) => $o->setProductReference($n->getStringValue()),
            'type' => fn(ParseNode $n) => $o->setType($n->getEnumValue(DiscountRedemptionData_type::class)),
        ];
    }

    /**
     * Gets the product_reference property value. The item number of the one product the discount is for; null when it counts on the whole purchase.
     * @return string|null
    */
    public function getProductReference(): ?string {
        return $this->product_reference;
    }

    /**
     * Gets the type property value. Which shape this is.
     * @return DiscountRedemptionData_type|null
    */
    public function getType(): ?DiscountRedemptionData_type {
        return $this->type;
    }

    /**
     * Serializes information the current object
     * @param SerializationWriter $writer Serialization writer to use to serialize this model
    */
    public function serialize(SerializationWriter $writer): void {
        $writer->writeEnumValue('discount_type', $this->getDiscountType());
        $writer->writeIntegerValue('discount_value', $this->getDiscountValue());
        $writer->writeStringValue('product_reference', $this->getProductReference());
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
     * Sets the discount_type property value. Which of the two `discount_value` is.
     * @param DiscountRedemptionData_discount_type|null $value Value to set for the discount_type property.
    */
    public function setDiscountType(?DiscountRedemptionData_discount_type $value): void {
        $this->discount_type = $value;
    }

    /**
     * Sets the discount_value property value. How much comes off: a percentage, or an amount in cents.
     * @param int|null $value Value to set for the discount_value property.
    */
    public function setDiscountValue(?int $value): void {
        $this->discount_value = $value;
    }

    /**
     * Sets the product_reference property value. The item number of the one product the discount is for; null when it counts on the whole purchase.
     * @param string|null $value Value to set for the product_reference property.
    */
    public function setProductReference(?string $value): void {
        $this->product_reference = $value;
    }

    /**
     * Sets the type property value. Which shape this is.
     * @param DiscountRedemptionData_type|null $value Value to set for the type property.
    */
    public function setType(?DiscountRedemptionData_type $value): void {
        $this->type = $value;
    }

}
