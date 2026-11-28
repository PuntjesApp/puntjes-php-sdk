<?php

namespace Puntjes\Generated\Models;

use Microsoft\Kiota\Abstractions\Serialization\AdditionalDataHolder;
use Microsoft\Kiota\Abstractions\Serialization\Parsable;
use Microsoft\Kiota\Abstractions\Serialization\ParseNode;
use Microsoft\Kiota\Abstractions\Serialization\SerializationWriter;

class GiftCardApiData implements AdditionalDataHolder, Parsable 
{
    /**
     * @var array<string, mixed>|null $additionalData Stores additional data not described in the OpenAPI description found when deserializing. Can be used for serialization as well.
    */
    private ?array $additionalData = null;
    
    /**
     * @var int|null $amount_left_cents What the gift card still holds, in cents.
    */
    private ?int $amount_left_cents = null;
    
    /**
     * @var string|null $code_last4 The last 4 characters of the code, for the receipt.
    */
    private ?string $code_last4 = null;
    
    /**
     * @var string|null $ends_on The last day the gift card works (YYYY-MM-DD, Brussels time).
    */
    private ?string $ends_on = null;
    
    /**
     * @var GiftCardStatus|null $status Whether a gift card can still be used: cancelled wins over used up, used up over ended, otherwise open.
    */
    private ?GiftCardStatus $status = null;
    
    /**
     * @var GiftCardVatKind|null $vat_kind Whether the shop knows at the sale what the gift card buys, which decides the VAT moment.
    */
    private ?GiftCardVatKind $vat_kind = null;
    
    /**
     * Instantiates a new GiftCardApiData and sets the default values.
    */
    public function __construct() {
        $this->setAdditionalData([]);
    }

    /**
     * Creates a new instance of the appropriate class based on discriminator value
     * @param ParseNode $parseNode The parse node to use to read the discriminator value and create the object
     * @return GiftCardApiData
    */
    public static function createFromDiscriminatorValue(ParseNode $parseNode): GiftCardApiData {
        return new GiftCardApiData();
    }

    /**
     * Gets the AdditionalData property value. Stores additional data not described in the OpenAPI description found when deserializing. Can be used for serialization as well.
     * @return array<string, mixed>|null
    */
    public function getAdditionalData(): ?array {
        return $this->additionalData;
    }

    /**
     * Gets the amount_left_cents property value. What the gift card still holds, in cents.
     * @return int|null
    */
    public function getAmountLeftCents(): ?int {
        return $this->amount_left_cents;
    }

    /**
     * Gets the code_last4 property value. The last 4 characters of the code, for the receipt.
     * @return string|null
    */
    public function getCodeLast4(): ?string {
        return $this->code_last4;
    }

    /**
     * Gets the ends_on property value. The last day the gift card works (YYYY-MM-DD, Brussels time).
     * @return string|null
    */
    public function getEndsOn(): ?string {
        return $this->ends_on;
    }

    /**
     * The deserialization information for the current model
     * @return array<string, callable(ParseNode): void>
    */
    public function getFieldDeserializers(): array {
        $o = $this;
        return  [
            'amount_left_cents' => fn(ParseNode $n) => $o->setAmountLeftCents($n->getIntegerValue()),
            'code_last4' => fn(ParseNode $n) => $o->setCodeLast4($n->getStringValue()),
            'ends_on' => fn(ParseNode $n) => $o->setEndsOn($n->getStringValue()),
            'status' => fn(ParseNode $n) => $o->setStatus($n->getEnumValue(GiftCardStatus::class)),
            'vat_kind' => fn(ParseNode $n) => $o->setVatKind($n->getEnumValue(GiftCardVatKind::class)),
        ];
    }

    /**
     * Gets the status property value. Whether a gift card can still be used: cancelled wins over used up, used up over ended, otherwise open.
     * @return GiftCardStatus|null
    */
    public function getStatus(): ?GiftCardStatus {
        return $this->status;
    }

    /**
     * Gets the vat_kind property value. Whether the shop knows at the sale what the gift card buys, which decides the VAT moment.
     * @return GiftCardVatKind|null
    */
    public function getVatKind(): ?GiftCardVatKind {
        return $this->vat_kind;
    }

    /**
     * Serializes information the current object
     * @param SerializationWriter $writer Serialization writer to use to serialize this model
    */
    public function serialize(SerializationWriter $writer): void {
        $writer->writeIntegerValue('amount_left_cents', $this->getAmountLeftCents());
        $writer->writeStringValue('code_last4', $this->getCodeLast4());
        $writer->writeStringValue('ends_on', $this->getEndsOn());
        $writer->writeEnumValue('status', $this->getStatus());
        $writer->writeEnumValue('vat_kind', $this->getVatKind());
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
     * Sets the amount_left_cents property value. What the gift card still holds, in cents.
     * @param int|null $value Value to set for the amount_left_cents property.
    */
    public function setAmountLeftCents(?int $value): void {
        $this->amount_left_cents = $value;
    }

    /**
     * Sets the code_last4 property value. The last 4 characters of the code, for the receipt.
     * @param string|null $value Value to set for the code_last4 property.
    */
    public function setCodeLast4(?string $value): void {
        $this->code_last4 = $value;
    }

    /**
     * Sets the ends_on property value. The last day the gift card works (YYYY-MM-DD, Brussels time).
     * @param string|null $value Value to set for the ends_on property.
    */
    public function setEndsOn(?string $value): void {
        $this->ends_on = $value;
    }

    /**
     * Sets the status property value. Whether a gift card can still be used: cancelled wins over used up, used up over ended, otherwise open.
     * @param GiftCardStatus|null $value Value to set for the status property.
    */
    public function setStatus(?GiftCardStatus $value): void {
        $this->status = $value;
    }

    /**
     * Sets the vat_kind property value. Whether the shop knows at the sale what the gift card buys, which decides the VAT moment.
     * @param GiftCardVatKind|null $value Value to set for the vat_kind property.
    */
    public function setVatKind(?GiftCardVatKind $value): void {
        $this->vat_kind = $value;
    }

}
