<?php

namespace Puntjes\Spike\Kiota\Models;

use Microsoft\Kiota\Abstractions\Serialization\AdditionalDataHolder;
use Microsoft\Kiota\Abstractions\Serialization\Parsable;
use Microsoft\Kiota\Abstractions\Serialization\ParseNode;
use Microsoft\Kiota\Abstractions\Serialization\SerializationWriter;

class FixedDiscountData implements AdditionalDataHolder, Parsable 
{
    /**
     * @var array<string, mixed>|null $additionalData Stores additional data not described in the OpenAPI description found when deserializing. Can be used for serialization as well.
    */
    private ?array $additionalData = null;
    
    /**
     * @var int|null $amount_cents How much to take off, in cents.
    */
    private ?int $amount_cents = null;
    
    /**
     * @var FixedDiscountData_kind|null $kind Deprecated: read `type`, which carries the same value. Always `fixed` for this shape.
    */
    private ?FixedDiscountData_kind $kind = null;
    
    /**
     * @var int|null $product_id The one product the discount is for; leave it out for the whole purchase.
    */
    private ?int $product_id = null;
    
    /**
     * @var FixedDiscountData_type|null $type Which shape this is.
    */
    private ?FixedDiscountData_type $type = null;
    
    /**
     * Instantiates a new FixedDiscountData and sets the default values.
    */
    public function __construct() {
        $this->setAdditionalData([]);
    }

    /**
     * Creates a new instance of the appropriate class based on discriminator value
     * @param ParseNode $parseNode The parse node to use to read the discriminator value and create the object
     * @return FixedDiscountData
    */
    public static function createFromDiscriminatorValue(ParseNode $parseNode): FixedDiscountData {
        return new FixedDiscountData();
    }

    /**
     * Gets the AdditionalData property value. Stores additional data not described in the OpenAPI description found when deserializing. Can be used for serialization as well.
     * @return array<string, mixed>|null
    */
    public function getAdditionalData(): ?array {
        return $this->additionalData;
    }

    /**
     * Gets the amount_cents property value. How much to take off, in cents.
     * @return int|null
    */
    public function getAmountCents(): ?int {
        return $this->amount_cents;
    }

    /**
     * The deserialization information for the current model
     * @return array<string, callable(ParseNode): void>
    */
    public function getFieldDeserializers(): array {
        $o = $this;
        return  [
            'amount_cents' => fn(ParseNode $n) => $o->setAmountCents($n->getIntegerValue()),
            'kind' => fn(ParseNode $n) => $o->setKind($n->getEnumValue(FixedDiscountData_kind::class)),
            'product_id' => fn(ParseNode $n) => $o->setProductId($n->getIntegerValue()),
            'type' => fn(ParseNode $n) => $o->setType($n->getEnumValue(FixedDiscountData_type::class)),
        ];
    }

    /**
     * Gets the kind property value. Deprecated: read `type`, which carries the same value. Always `fixed` for this shape.
     * @return FixedDiscountData_kind|null
    */
    public function getKind(): ?FixedDiscountData_kind {
        return $this->kind;
    }

    /**
     * Gets the product_id property value. The one product the discount is for; leave it out for the whole purchase.
     * @return int|null
    */
    public function getProductId(): ?int {
        return $this->product_id;
    }

    /**
     * Gets the type property value. Which shape this is.
     * @return FixedDiscountData_type|null
    */
    public function getType(): ?FixedDiscountData_type {
        return $this->type;
    }

    /**
     * Serializes information the current object
     * @param SerializationWriter $writer Serialization writer to use to serialize this model
    */
    public function serialize(SerializationWriter $writer): void {
        $writer->writeIntegerValue('amount_cents', $this->getAmountCents());
        $writer->writeEnumValue('kind', $this->getKind());
        $writer->writeIntegerValue('product_id', $this->getProductId());
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
     * Sets the amount_cents property value. How much to take off, in cents.
     * @param int|null $value Value to set for the amount_cents property.
    */
    public function setAmountCents(?int $value): void {
        $this->amount_cents = $value;
    }

    /**
     * Sets the kind property value. Deprecated: read `type`, which carries the same value. Always `fixed` for this shape.
     * @param FixedDiscountData_kind|null $value Value to set for the kind property.
    */
    public function setKind(?FixedDiscountData_kind $value): void {
        $this->kind = $value;
    }

    /**
     * Sets the product_id property value. The one product the discount is for; leave it out for the whole purchase.
     * @param int|null $value Value to set for the product_id property.
    */
    public function setProductId(?int $value): void {
        $this->product_id = $value;
    }

    /**
     * Sets the type property value. Which shape this is.
     * @param FixedDiscountData_type|null $value Value to set for the type property.
    */
    public function setType(?FixedDiscountData_type $value): void {
        $this->type = $value;
    }

}
