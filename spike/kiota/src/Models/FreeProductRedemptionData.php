<?php

namespace Puntjes\Spike\Kiota\Models;

use Microsoft\Kiota\Abstractions\Serialization\AdditionalDataHolder;
use Microsoft\Kiota\Abstractions\Serialization\Parsable;
use Microsoft\Kiota\Abstractions\Serialization\ParseNode;
use Microsoft\Kiota\Abstractions\Serialization\SerializationWriter;

class FreeProductRedemptionData implements AdditionalDataHolder, Parsable 
{
    /**
     * @var array<string, mixed>|null $additionalData Stores additional data not described in the OpenAPI description found when deserializing. Can be used for serialization as well.
    */
    private ?array $additionalData = null;
    
    /**
     * @var int|null $payment_amount What the till collects on top of the points, in cents; `0` when the points cover it.
    */
    private ?int $payment_amount = null;
    
    /**
     * @var string|null $product_reference Your own identifier for the product to hand over.
    */
    private ?string $product_reference = null;
    
    /**
     * @var FreeProductRedemptionData_type|null $type Which shape this is.
    */
    private ?FreeProductRedemptionData_type $type = null;
    
    /**
     * Instantiates a new FreeProductRedemptionData and sets the default values.
    */
    public function __construct() {
        $this->setAdditionalData([]);
    }

    /**
     * Creates a new instance of the appropriate class based on discriminator value
     * @param ParseNode $parseNode The parse node to use to read the discriminator value and create the object
     * @return FreeProductRedemptionData
    */
    public static function createFromDiscriminatorValue(ParseNode $parseNode): FreeProductRedemptionData {
        return new FreeProductRedemptionData();
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
            'payment_amount' => fn(ParseNode $n) => $o->setPaymentAmount($n->getIntegerValue()),
            'product_reference' => fn(ParseNode $n) => $o->setProductReference($n->getStringValue()),
            'type' => fn(ParseNode $n) => $o->setType($n->getEnumValue(FreeProductRedemptionData_type::class)),
        ];
    }

    /**
     * Gets the payment_amount property value. What the till collects on top of the points, in cents; `0` when the points cover it.
     * @return int|null
    */
    public function getPaymentAmount(): ?int {
        return $this->payment_amount;
    }

    /**
     * Gets the product_reference property value. Your own identifier for the product to hand over.
     * @return string|null
    */
    public function getProductReference(): ?string {
        return $this->product_reference;
    }

    /**
     * Gets the type property value. Which shape this is.
     * @return FreeProductRedemptionData_type|null
    */
    public function getType(): ?FreeProductRedemptionData_type {
        return $this->type;
    }

    /**
     * Serializes information the current object
     * @param SerializationWriter $writer Serialization writer to use to serialize this model
    */
    public function serialize(SerializationWriter $writer): void {
        $writer->writeIntegerValue('payment_amount', $this->getPaymentAmount());
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
     * Sets the payment_amount property value. What the till collects on top of the points, in cents; `0` when the points cover it.
     * @param int|null $value Value to set for the payment_amount property.
    */
    public function setPaymentAmount(?int $value): void {
        $this->payment_amount = $value;
    }

    /**
     * Sets the product_reference property value. Your own identifier for the product to hand over.
     * @param string|null $value Value to set for the product_reference property.
    */
    public function setProductReference(?string $value): void {
        $this->product_reference = $value;
    }

    /**
     * Sets the type property value. Which shape this is.
     * @param FreeProductRedemptionData_type|null $value Value to set for the type property.
    */
    public function setType(?FreeProductRedemptionData_type $value): void {
        $this->type = $value;
    }

}
