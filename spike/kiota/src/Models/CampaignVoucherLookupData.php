<?php

namespace Puntjes\Spike\Kiota\Models;

use Microsoft\Kiota\Abstractions\Serialization\AdditionalDataHolder;
use Microsoft\Kiota\Abstractions\Serialization\Parsable;
use Microsoft\Kiota\Abstractions\Serialization\ParseNode;
use Microsoft\Kiota\Abstractions\Serialization\SerializationWriter;

class CampaignVoucherLookupData implements AdditionalDataHolder, Parsable 
{
    /**
     * @var array<string, mixed>|null $additionalData Stores additional data not described in the OpenAPI description found when deserializing. Can be used for serialization as well.
    */
    private ?array $additionalData = null;
    
    /**
     * @var int|null $campaign_id The campaign_id property
    */
    private ?int $campaign_id = null;
    
    /**
     * @var string|null $consumed_at When the bon was spent, or null while it is not.
    */
    private ?string $consumed_at = null;
    
    /**
     * @var CampaignVoucherLookupData_discount|null $discount How much comes off, for a discount bon. Its `type` says which shape this is.
    */
    private ?CampaignVoucherLookupData_discount $discount = null;
    
    /**
     * @var string|null $kind The kind property
    */
    private ?string $kind = null;
    
    /**
     * @var array<VoucherGiftProductData>|null $products What to hand over, for a free-product bon.
    */
    private ?array $products = null;
    
    /**
     * @var string|null $status `valid`, `used` or `expired`.
    */
    private ?string $status = null;
    
    /**
     * @var string|null $valid_until The valid_until property
    */
    private ?string $valid_until = null;
    
    /**
     * @var string|null $voucher_code The voucher_code property
    */
    private ?string $voucher_code = null;
    
    /**
     * Instantiates a new CampaignVoucherLookupData and sets the default values.
    */
    public function __construct() {
        $this->setAdditionalData([]);
    }

    /**
     * Creates a new instance of the appropriate class based on discriminator value
     * @param ParseNode $parseNode The parse node to use to read the discriminator value and create the object
     * @return CampaignVoucherLookupData
    */
    public static function createFromDiscriminatorValue(ParseNode $parseNode): CampaignVoucherLookupData {
        return new CampaignVoucherLookupData();
    }

    /**
     * Gets the AdditionalData property value. Stores additional data not described in the OpenAPI description found when deserializing. Can be used for serialization as well.
     * @return array<string, mixed>|null
    */
    public function getAdditionalData(): ?array {
        return $this->additionalData;
    }

    /**
     * Gets the campaign_id property value. The campaign_id property
     * @return int|null
    */
    public function getCampaignId(): ?int {
        return $this->campaign_id;
    }

    /**
     * Gets the consumed_at property value. When the bon was spent, or null while it is not.
     * @return string|null
    */
    public function getConsumedAt(): ?string {
        return $this->consumed_at;
    }

    /**
     * Gets the discount property value. How much comes off, for a discount bon. Its `type` says which shape this is.
     * @return CampaignVoucherLookupData_discount|null
    */
    public function getDiscount(): ?CampaignVoucherLookupData_discount {
        return $this->discount;
    }

    /**
     * The deserialization information for the current model
     * @return array<string, callable(ParseNode): void>
    */
    public function getFieldDeserializers(): array {
        $o = $this;
        return  [
            'campaign_id' => fn(ParseNode $n) => $o->setCampaignId($n->getIntegerValue()),
            'consumed_at' => fn(ParseNode $n) => $o->setConsumedAt($n->getStringValue()),
            'discount' => fn(ParseNode $n) => $o->setDiscount($n->getObjectValue([CampaignVoucherLookupData_discount::class, 'createFromDiscriminatorValue'])),
            'kind' => fn(ParseNode $n) => $o->setKind($n->getStringValue()),
            'products' => fn(ParseNode $n) => $o->setProducts($n->getCollectionOfObjectValues([VoucherGiftProductData::class, 'createFromDiscriminatorValue'])),
            'status' => fn(ParseNode $n) => $o->setStatus($n->getStringValue()),
            'valid_until' => fn(ParseNode $n) => $o->setValidUntil($n->getStringValue()),
            'voucher_code' => fn(ParseNode $n) => $o->setVoucherCode($n->getStringValue()),
        ];
    }

    /**
     * Gets the kind property value. The kind property
     * @return string|null
    */
    public function getKind(): ?string {
        return $this->kind;
    }

    /**
     * Gets the products property value. What to hand over, for a free-product bon.
     * @return array<VoucherGiftProductData>|null
    */
    public function getProducts(): ?array {
        return $this->products;
    }

    /**
     * Gets the status property value. `valid`, `used` or `expired`.
     * @return string|null
    */
    public function getStatus(): ?string {
        return $this->status;
    }

    /**
     * Gets the valid_until property value. The valid_until property
     * @return string|null
    */
    public function getValidUntil(): ?string {
        return $this->valid_until;
    }

    /**
     * Gets the voucher_code property value. The voucher_code property
     * @return string|null
    */
    public function getVoucherCode(): ?string {
        return $this->voucher_code;
    }

    /**
     * Serializes information the current object
     * @param SerializationWriter $writer Serialization writer to use to serialize this model
    */
    public function serialize(SerializationWriter $writer): void {
        $writer->writeIntegerValue('campaign_id', $this->getCampaignId());
        $writer->writeStringValue('consumed_at', $this->getConsumedAt());
        $writer->writeObjectValue('discount', $this->getDiscount());
        $writer->writeStringValue('kind', $this->getKind());
        $writer->writeCollectionOfObjectValues('products', $this->getProducts());
        $writer->writeStringValue('status', $this->getStatus());
        $writer->writeStringValue('valid_until', $this->getValidUntil());
        $writer->writeStringValue('voucher_code', $this->getVoucherCode());
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
     * Sets the campaign_id property value. The campaign_id property
     * @param int|null $value Value to set for the campaign_id property.
    */
    public function setCampaignId(?int $value): void {
        $this->campaign_id = $value;
    }

    /**
     * Sets the consumed_at property value. When the bon was spent, or null while it is not.
     * @param string|null $value Value to set for the consumed_at property.
    */
    public function setConsumedAt(?string $value): void {
        $this->consumed_at = $value;
    }

    /**
     * Sets the discount property value. How much comes off, for a discount bon. Its `type` says which shape this is.
     * @param CampaignVoucherLookupData_discount|null $value Value to set for the discount property.
    */
    public function setDiscount(?CampaignVoucherLookupData_discount $value): void {
        $this->discount = $value;
    }

    /**
     * Sets the kind property value. The kind property
     * @param string|null $value Value to set for the kind property.
    */
    public function setKind(?string $value): void {
        $this->kind = $value;
    }

    /**
     * Sets the products property value. What to hand over, for a free-product bon.
     * @param array<VoucherGiftProductData>|null $value Value to set for the products property.
    */
    public function setProducts(?array $value): void {
        $this->products = $value;
    }

    /**
     * Sets the status property value. `valid`, `used` or `expired`.
     * @param string|null $value Value to set for the status property.
    */
    public function setStatus(?string $value): void {
        $this->status = $value;
    }

    /**
     * Sets the valid_until property value. The valid_until property
     * @param string|null $value Value to set for the valid_until property.
    */
    public function setValidUntil(?string $value): void {
        $this->valid_until = $value;
    }

    /**
     * Sets the voucher_code property value. The voucher_code property
     * @param string|null $value Value to set for the voucher_code property.
    */
    public function setVoucherCode(?string $value): void {
        $this->voucher_code = $value;
    }

}
