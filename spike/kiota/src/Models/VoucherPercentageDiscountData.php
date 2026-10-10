<?php

namespace Puntjes\Spike\Kiota\Models;

use Microsoft\Kiota\Abstractions\Serialization\AdditionalDataHolder;
use Microsoft\Kiota\Abstractions\Serialization\Parsable;
use Microsoft\Kiota\Abstractions\Serialization\ParseNode;
use Microsoft\Kiota\Abstractions\Serialization\SerializationWriter;

class VoucherPercentageDiscountData implements AdditionalDataHolder, Parsable 
{
    /**
     * @var array<string, mixed>|null $additionalData Stores additional data not described in the OpenAPI description found when deserializing. Can be used for serialization as well.
    */
    private ?array $additionalData = null;
    
    /**
     * @var VoucherPercentageDiscountData_kind|null $kind Deprecated: read `type`, which carries the same value. Always `percentage` for this shape.
    */
    private ?VoucherPercentageDiscountData_kind $kind = null;
    
    /**
     * @var int|null $percentage How many percent to take off, 1 to 100.
    */
    private ?int $percentage = null;
    
    /**
     * @var string|null $product_reference The item number of the one product the discount is for; null for the whole purchase.
    */
    private ?string $product_reference = null;
    
    /**
     * @var VoucherPercentageDiscountData_type|null $type Which shape this is.
    */
    private ?VoucherPercentageDiscountData_type $type = null;
    
    /**
     * Instantiates a new VoucherPercentageDiscountData and sets the default values.
    */
    public function __construct() {
        $this->setAdditionalData([]);
    }

    /**
     * Creates a new instance of the appropriate class based on discriminator value
     * @param ParseNode $parseNode The parse node to use to read the discriminator value and create the object
     * @return VoucherPercentageDiscountData
    */
    public static function createFromDiscriminatorValue(ParseNode $parseNode): VoucherPercentageDiscountData {
        return new VoucherPercentageDiscountData();
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
            'kind' => fn(ParseNode $n) => $o->setKind($n->getEnumValue(VoucherPercentageDiscountData_kind::class)),
            'percentage' => fn(ParseNode $n) => $o->setPercentage($n->getIntegerValue()),
            'product_reference' => fn(ParseNode $n) => $o->setProductReference($n->getStringValue()),
            'type' => fn(ParseNode $n) => $o->setType($n->getEnumValue(VoucherPercentageDiscountData_type::class)),
        ];
    }

    /**
     * Gets the kind property value. Deprecated: read `type`, which carries the same value. Always `percentage` for this shape.
     * @return VoucherPercentageDiscountData_kind|null
    */
    public function getKind(): ?VoucherPercentageDiscountData_kind {
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
     * Gets the product_reference property value. The item number of the one product the discount is for; null for the whole purchase.
     * @return string|null
    */
    public function getProductReference(): ?string {
        return $this->product_reference;
    }

    /**
     * Gets the type property value. Which shape this is.
     * @return VoucherPercentageDiscountData_type|null
    */
    public function getType(): ?VoucherPercentageDiscountData_type {
        return $this->type;
    }

    /**
     * Serializes information the current object
     * @param SerializationWriter $writer Serialization writer to use to serialize this model
    */
    public function serialize(SerializationWriter $writer): void {
        $writer->writeEnumValue('kind', $this->getKind());
        $writer->writeIntegerValue('percentage', $this->getPercentage());
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
     * Sets the kind property value. Deprecated: read `type`, which carries the same value. Always `percentage` for this shape.
     * @param VoucherPercentageDiscountData_kind|null $value Value to set for the kind property.
    */
    public function setKind(?VoucherPercentageDiscountData_kind $value): void {
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
     * Sets the product_reference property value. The item number of the one product the discount is for; null for the whole purchase.
     * @param string|null $value Value to set for the product_reference property.
    */
    public function setProductReference(?string $value): void {
        $this->product_reference = $value;
    }

    /**
     * Sets the type property value. Which shape this is.
     * @param VoucherPercentageDiscountData_type|null $value Value to set for the type property.
    */
    public function setType(?VoucherPercentageDiscountData_type $value): void {
        $this->type = $value;
    }

}
