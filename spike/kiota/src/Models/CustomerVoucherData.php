<?php

namespace Puntjes\Spike\Kiota\Models;

use Microsoft\Kiota\Abstractions\Serialization\AdditionalDataHolder;
use Microsoft\Kiota\Abstractions\Serialization\Parsable;
use Microsoft\Kiota\Abstractions\Serialization\ParseNode;
use Microsoft\Kiota\Abstractions\Serialization\SerializationWriter;

class CustomerVoucherData implements AdditionalDataHolder, Parsable 
{
    /**
     * @var array<string, mixed>|null $additionalData Stores additional data not described in the OpenAPI description found when deserializing. Can be used for serialization as well.
    */
    private ?array $additionalData = null;
    
    /**
     * @var array<BranchReferenceData>|null $branches The branches that take the bon; null means every branch.
    */
    private ?array $branches = null;
    
    /**
     * @var int|null $campaign_id The campaign_id property
    */
    private ?int $campaign_id = null;
    
    /**
     * @var CustomerVoucherData_discount|null $discount How much comes off, for a discount bon. Its `type` says which shape this is.
    */
    private ?CustomerVoucherData_discount $discount = null;
    
    /**
     * @var string|null $kind The kind property
    */
    private ?string $kind = null;
    
    /**
     * @var array<VoucherGiftProductData>|null $products What to hand over, for a free-product bon.
    */
    private ?array $products = null;
    
    /**
     * @var string|null $valid_until The last day the bon can be spent, or null when it does not run out.
    */
    private ?string $valid_until = null;
    
    /**
     * @var string|null $voucher_code The voucher_code property
    */
    private ?string $voucher_code = null;
    
    /**
     * Instantiates a new CustomerVoucherData and sets the default values.
    */
    public function __construct() {
        $this->setAdditionalData([]);
    }

    /**
     * Creates a new instance of the appropriate class based on discriminator value
     * @param ParseNode $parseNode The parse node to use to read the discriminator value and create the object
     * @return CustomerVoucherData
    */
    public static function createFromDiscriminatorValue(ParseNode $parseNode): CustomerVoucherData {
        return new CustomerVoucherData();
    }

    /**
     * Gets the AdditionalData property value. Stores additional data not described in the OpenAPI description found when deserializing. Can be used for serialization as well.
     * @return array<string, mixed>|null
    */
    public function getAdditionalData(): ?array {
        return $this->additionalData;
    }

    /**
     * Gets the branches property value. The branches that take the bon; null means every branch.
     * @return array<BranchReferenceData>|null
    */
    public function getBranches(): ?array {
        return $this->branches;
    }

    /**
     * Gets the campaign_id property value. The campaign_id property
     * @return int|null
    */
    public function getCampaignId(): ?int {
        return $this->campaign_id;
    }

    /**
     * Gets the discount property value. How much comes off, for a discount bon. Its `type` says which shape this is.
     * @return CustomerVoucherData_discount|null
    */
    public function getDiscount(): ?CustomerVoucherData_discount {
        return $this->discount;
    }

    /**
     * The deserialization information for the current model
     * @return array<string, callable(ParseNode): void>
    */
    public function getFieldDeserializers(): array {
        $o = $this;
        return  [
            'branches' => fn(ParseNode $n) => $o->setBranches($n->getCollectionOfObjectValues([BranchReferenceData::class, 'createFromDiscriminatorValue'])),
            'campaign_id' => fn(ParseNode $n) => $o->setCampaignId($n->getIntegerValue()),
            'discount' => fn(ParseNode $n) => $o->setDiscount($n->getObjectValue([CustomerVoucherData_discount::class, 'createFromDiscriminatorValue'])),
            'kind' => fn(ParseNode $n) => $o->setKind($n->getStringValue()),
            'products' => fn(ParseNode $n) => $o->setProducts($n->getCollectionOfObjectValues([VoucherGiftProductData::class, 'createFromDiscriminatorValue'])),
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
     * Gets the valid_until property value. The last day the bon can be spent, or null when it does not run out.
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
        $writer->writeCollectionOfObjectValues('branches', $this->getBranches());
        $writer->writeIntegerValue('campaign_id', $this->getCampaignId());
        $writer->writeObjectValue('discount', $this->getDiscount());
        $writer->writeStringValue('kind', $this->getKind());
        $writer->writeCollectionOfObjectValues('products', $this->getProducts());
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
     * Sets the branches property value. The branches that take the bon; null means every branch.
     * @param array<BranchReferenceData>|null $value Value to set for the branches property.
    */
    public function setBranches(?array $value): void {
        $this->branches = $value;
    }

    /**
     * Sets the campaign_id property value. The campaign_id property
     * @param int|null $value Value to set for the campaign_id property.
    */
    public function setCampaignId(?int $value): void {
        $this->campaign_id = $value;
    }

    /**
     * Sets the discount property value. How much comes off, for a discount bon. Its `type` says which shape this is.
     * @param CustomerVoucherData_discount|null $value Value to set for the discount property.
    */
    public function setDiscount(?CustomerVoucherData_discount $value): void {
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
     * Sets the valid_until property value. The last day the bon can be spent, or null when it does not run out.
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
