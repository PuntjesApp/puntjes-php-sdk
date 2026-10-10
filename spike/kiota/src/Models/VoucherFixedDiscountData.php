<?php

namespace Puntjes\Spike\Kiota\Models;

use Microsoft\Kiota\Abstractions\Serialization\AdditionalDataHolder;
use Microsoft\Kiota\Abstractions\Serialization\Parsable;
use Microsoft\Kiota\Abstractions\Serialization\ParseNode;
use Microsoft\Kiota\Abstractions\Serialization\SerializationWriter;

class VoucherFixedDiscountData implements AdditionalDataHolder, Parsable 
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
     * @var VoucherFixedDiscountData_kind|null $kind Deprecated: read `type`, which carries the same value. Always `fixed` for this shape.
    */
    private ?VoucherFixedDiscountData_kind $kind = null;
    
    /**
     * @var string|null $product_reference The item number of the one product the discount is for; null for the whole purchase.
    */
    private ?string $product_reference = null;
    
    /**
     * @var VoucherFixedDiscountData_type|null $type Which shape this is.
    */
    private ?VoucherFixedDiscountData_type $type = null;
    
    /**
     * Instantiates a new VoucherFixedDiscountData and sets the default values.
    */
    public function __construct() {
        $this->setAdditionalData([]);
    }

    /**
     * Creates a new instance of the appropriate class based on discriminator value
     * @param ParseNode $parseNode The parse node to use to read the discriminator value and create the object
     * @return VoucherFixedDiscountData
    */
    public static function createFromDiscriminatorValue(ParseNode $parseNode): VoucherFixedDiscountData {
        return new VoucherFixedDiscountData();
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
            'kind' => fn(ParseNode $n) => $o->setKind($n->getEnumValue(VoucherFixedDiscountData_kind::class)),
            'product_reference' => fn(ParseNode $n) => $o->setProductReference($n->getStringValue()),
            'type' => fn(ParseNode $n) => $o->setType($n->getEnumValue(VoucherFixedDiscountData_type::class)),
        ];
    }

    /**
     * Gets the kind property value. Deprecated: read `type`, which carries the same value. Always `fixed` for this shape.
     * @return VoucherFixedDiscountData_kind|null
    */
    public function getKind(): ?VoucherFixedDiscountData_kind {
        return $this->kind;
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
     * @return VoucherFixedDiscountData_type|null
    */
    public function getType(): ?VoucherFixedDiscountData_type {
        return $this->type;
    }

    /**
     * Serializes information the current object
     * @param SerializationWriter $writer Serialization writer to use to serialize this model
    */
    public function serialize(SerializationWriter $writer): void {
        $writer->writeIntegerValue('amount_cents', $this->getAmountCents());
        $writer->writeEnumValue('kind', $this->getKind());
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
     * Sets the amount_cents property value. How much to take off, in cents.
     * @param int|null $value Value to set for the amount_cents property.
    */
    public function setAmountCents(?int $value): void {
        $this->amount_cents = $value;
    }

    /**
     * Sets the kind property value. Deprecated: read `type`, which carries the same value. Always `fixed` for this shape.
     * @param VoucherFixedDiscountData_kind|null $value Value to set for the kind property.
    */
    public function setKind(?VoucherFixedDiscountData_kind $value): void {
        $this->kind = $value;
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
     * @param VoucherFixedDiscountData_type|null $value Value to set for the type property.
    */
    public function setType(?VoucherFixedDiscountData_type $value): void {
        $this->type = $value;
    }

}
