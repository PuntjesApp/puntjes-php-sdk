<?php

namespace Puntjes\Spike\Kiota\Models;

use Microsoft\Kiota\Abstractions\Serialization\AdditionalDataHolder;
use Microsoft\Kiota\Abstractions\Serialization\Parsable;
use Microsoft\Kiota\Abstractions\Serialization\ParseNode;
use Microsoft\Kiota\Abstractions\Serialization\SerializationWriter;

class PercentageDiscountData implements AdditionalDataHolder, Parsable 
{
    /**
     * @var array<string, mixed>|null $additionalData Stores additional data not described in the OpenAPI description found when deserializing. Can be used for serialization as well.
    */
    private ?array $additionalData = null;
    
    /**
     * @var PercentageDiscountData_kind|null $kind Deprecated: read `type`, which carries the same value. Always `percentage` for this shape.
    */
    private ?PercentageDiscountData_kind $kind = null;
    
    /**
     * @var int|null $percentage How many percent to take off, 1 to 100.
    */
    private ?int $percentage = null;
    
    /**
     * @var int|null $product_id The one product the discount is for; leave it out for the whole purchase.
    */
    private ?int $product_id = null;
    
    /**
     * @var PercentageDiscountData_type|null $type Which shape this is.
    */
    private ?PercentageDiscountData_type $type = null;
    
    /**
     * Instantiates a new PercentageDiscountData and sets the default values.
    */
    public function __construct() {
        $this->setAdditionalData([]);
    }

    /**
     * Creates a new instance of the appropriate class based on discriminator value
     * @param ParseNode $parseNode The parse node to use to read the discriminator value and create the object
     * @return PercentageDiscountData
    */
    public static function createFromDiscriminatorValue(ParseNode $parseNode): PercentageDiscountData {
        return new PercentageDiscountData();
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
            'kind' => fn(ParseNode $n) => $o->setKind($n->getEnumValue(PercentageDiscountData_kind::class)),
            'percentage' => fn(ParseNode $n) => $o->setPercentage($n->getIntegerValue()),
            'product_id' => fn(ParseNode $n) => $o->setProductId($n->getIntegerValue()),
            'type' => fn(ParseNode $n) => $o->setType($n->getEnumValue(PercentageDiscountData_type::class)),
        ];
    }

    /**
     * Gets the kind property value. Deprecated: read `type`, which carries the same value. Always `percentage` for this shape.
     * @return PercentageDiscountData_kind|null
    */
    public function getKind(): ?PercentageDiscountData_kind {
        return $this->kind;
    }

    /**
     * Gets the percentage property value. How many percent to take off, 1 to 100.
     * @return int|null
    */
    public function getPercentage(): ?int {
        return $this->percentage;
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
     * @return PercentageDiscountData_type|null
    */
    public function getType(): ?PercentageDiscountData_type {
        return $this->type;
    }

    /**
     * Serializes information the current object
     * @param SerializationWriter $writer Serialization writer to use to serialize this model
    */
    public function serialize(SerializationWriter $writer): void {
        $writer->writeEnumValue('kind', $this->getKind());
        $writer->writeIntegerValue('percentage', $this->getPercentage());
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
     * Sets the kind property value. Deprecated: read `type`, which carries the same value. Always `percentage` for this shape.
     * @param PercentageDiscountData_kind|null $value Value to set for the kind property.
    */
    public function setKind(?PercentageDiscountData_kind $value): void {
        $this->kind = $value;
    }

    /**
     * Sets the percentage property value. How many percent to take off, 1 to 100.
     * @param int|null $value Value to set for the percentage property.
    */
    public function setPercentage(?int $value): void {
        $this->percentage = $value;
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
     * @param PercentageDiscountData_type|null $value Value to set for the type property.
    */
    public function setType(?PercentageDiscountData_type $value): void {
        $this->type = $value;
    }

}
