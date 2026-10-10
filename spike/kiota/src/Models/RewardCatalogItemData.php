<?php

namespace Puntjes\Spike\Kiota\Models;

use Microsoft\Kiota\Abstractions\Serialization\AdditionalDataHolder;
use Microsoft\Kiota\Abstractions\Serialization\Parsable;
use Microsoft\Kiota\Abstractions\Serialization\ParseNode;
use Microsoft\Kiota\Abstractions\Serialization\SerializationWriter;

class RewardCatalogItemData implements AdditionalDataHolder, Parsable 
{
    /**
     * @var array<string, mixed>|null $additionalData Stores additional data not described in the OpenAPI description found when deserializing. Can be used for serialization as well.
    */
    private ?array $additionalData = null;
    
    /**
     * @var string|null $available_from The available_from property
    */
    private ?string $available_from = null;
    
    /**
     * @var string|null $available_until The available_until property
    */
    private ?string $available_until = null;
    
    /**
     * @var array<BranchReferenceData>|null $branches null means every branch
    */
    private ?array $branches = null;
    
    /**
     * @var int|null $customer_redemptions How many times the customer named by identifier redeemed this reward, cancelled ones left out; null when no identifier was sent or it matched nobody.
    */
    private ?int $customer_redemptions = null;
    
    /**
     * @var string|null $description The description property
    */
    private ?string $description = null;
    
    /**
     * @var RewardCatalogItemData_discount_type|null $discount_type `percentage` or `fixed_amount` for a discount, null for a free product.
    */
    private ?RewardCatalogItemData_discount_type $discount_type = null;
    
    /**
     * @var int|null $discount_value A percentage as a whole number (10 is 10%), a fixed amount in cents; null for a free product.
    */
    private ?int $discount_value = null;
    
    /**
     * @var int|null $id The id property
    */
    private ?int $id = null;
    
    /**
     * @var string|null $image_url The image_url property
    */
    private ?string $image_url = null;
    
    /**
     * @var bool|null $is_unlimited True when the reward has no stock limit; remaining_stock is then 0.
    */
    private ?bool $is_unlimited = null;
    
    /**
     * @var int|null $max_redemptions_per_customer How many times one customer can redeem this reward; null means no limit.
    */
    private ?int $max_redemptions_per_customer = null;
    
    /**
     * @var string|null $name The name property
    */
    private ?string $name = null;
    
    /**
     * @var int|null $payment_amount The payment_amount property
    */
    private ?int $payment_amount = null;
    
    /**
     * @var int|null $point_cost The point_cost property
    */
    private ?int $point_cost = null;
    
    /**
     * @var string|null $product_reference The item number the reward is for. Read type first: null is the whole purchase for a discount, no item number for a free product.
    */
    private ?string $product_reference = null;
    
    /**
     * @var int|null $remaining_stock The remaining_stock property
    */
    private ?int $remaining_stock = null;
    
    /**
     * @var int|null $total_stock The total_stock property
    */
    private ?int $total_stock = null;
    
    /**
     * @var string|null $type The type property
    */
    private ?string $type = null;
    
    /**
     * Instantiates a new RewardCatalogItemData and sets the default values.
    */
    public function __construct() {
        $this->setAdditionalData([]);
    }

    /**
     * Creates a new instance of the appropriate class based on discriminator value
     * @param ParseNode $parseNode The parse node to use to read the discriminator value and create the object
     * @return RewardCatalogItemData
    */
    public static function createFromDiscriminatorValue(ParseNode $parseNode): RewardCatalogItemData {
        return new RewardCatalogItemData();
    }

    /**
     * Gets the AdditionalData property value. Stores additional data not described in the OpenAPI description found when deserializing. Can be used for serialization as well.
     * @return array<string, mixed>|null
    */
    public function getAdditionalData(): ?array {
        return $this->additionalData;
    }

    /**
     * Gets the available_from property value. The available_from property
     * @return string|null
    */
    public function getAvailableFrom(): ?string {
        return $this->available_from;
    }

    /**
     * Gets the available_until property value. The available_until property
     * @return string|null
    */
    public function getAvailableUntil(): ?string {
        return $this->available_until;
    }

    /**
     * Gets the branches property value. null means every branch
     * @return array<BranchReferenceData>|null
    */
    public function getBranches(): ?array {
        return $this->branches;
    }

    /**
     * Gets the customer_redemptions property value. How many times the customer named by identifier redeemed this reward, cancelled ones left out; null when no identifier was sent or it matched nobody.
     * @return int|null
    */
    public function getCustomerRedemptions(): ?int {
        return $this->customer_redemptions;
    }

    /**
     * Gets the description property value. The description property
     * @return string|null
    */
    public function getDescription(): ?string {
        return $this->description;
    }

    /**
     * Gets the discount_type property value. `percentage` or `fixed_amount` for a discount, null for a free product.
     * @return RewardCatalogItemData_discount_type|null
    */
    public function getDiscountType(): ?RewardCatalogItemData_discount_type {
        return $this->discount_type;
    }

    /**
     * Gets the discount_value property value. A percentage as a whole number (10 is 10%), a fixed amount in cents; null for a free product.
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
            'available_from' => fn(ParseNode $n) => $o->setAvailableFrom($n->getStringValue()),
            'available_until' => fn(ParseNode $n) => $o->setAvailableUntil($n->getStringValue()),
            'branches' => fn(ParseNode $n) => $o->setBranches($n->getCollectionOfObjectValues([BranchReferenceData::class, 'createFromDiscriminatorValue'])),
            'customer_redemptions' => fn(ParseNode $n) => $o->setCustomerRedemptions($n->getIntegerValue()),
            'description' => fn(ParseNode $n) => $o->setDescription($n->getStringValue()),
            'discount_type' => fn(ParseNode $n) => $o->setDiscountType($n->getEnumValue(RewardCatalogItemData_discount_type::class)),
            'discount_value' => fn(ParseNode $n) => $o->setDiscountValue($n->getIntegerValue()),
            'id' => fn(ParseNode $n) => $o->setId($n->getIntegerValue()),
            'image_url' => fn(ParseNode $n) => $o->setImageUrl($n->getStringValue()),
            'is_unlimited' => fn(ParseNode $n) => $o->setIsUnlimited($n->getBooleanValue()),
            'max_redemptions_per_customer' => fn(ParseNode $n) => $o->setMaxRedemptionsPerCustomer($n->getIntegerValue()),
            'name' => fn(ParseNode $n) => $o->setName($n->getStringValue()),
            'payment_amount' => fn(ParseNode $n) => $o->setPaymentAmount($n->getIntegerValue()),
            'point_cost' => fn(ParseNode $n) => $o->setPointCost($n->getIntegerValue()),
            'product_reference' => fn(ParseNode $n) => $o->setProductReference($n->getStringValue()),
            'remaining_stock' => fn(ParseNode $n) => $o->setRemainingStock($n->getIntegerValue()),
            'total_stock' => fn(ParseNode $n) => $o->setTotalStock($n->getIntegerValue()),
            'type' => fn(ParseNode $n) => $o->setType($n->getStringValue()),
        ];
    }

    /**
     * Gets the id property value. The id property
     * @return int|null
    */
    public function getId(): ?int {
        return $this->id;
    }

    /**
     * Gets the image_url property value. The image_url property
     * @return string|null
    */
    public function getImageUrl(): ?string {
        return $this->image_url;
    }

    /**
     * Gets the is_unlimited property value. True when the reward has no stock limit; remaining_stock is then 0.
     * @return bool|null
    */
    public function getIsUnlimited(): ?bool {
        return $this->is_unlimited;
    }

    /**
     * Gets the max_redemptions_per_customer property value. How many times one customer can redeem this reward; null means no limit.
     * @return int|null
    */
    public function getMaxRedemptionsPerCustomer(): ?int {
        return $this->max_redemptions_per_customer;
    }

    /**
     * Gets the name property value. The name property
     * @return string|null
    */
    public function getName(): ?string {
        return $this->name;
    }

    /**
     * Gets the payment_amount property value. The payment_amount property
     * @return int|null
    */
    public function getPaymentAmount(): ?int {
        return $this->payment_amount;
    }

    /**
     * Gets the point_cost property value. The point_cost property
     * @return int|null
    */
    public function getPointCost(): ?int {
        return $this->point_cost;
    }

    /**
     * Gets the product_reference property value. The item number the reward is for. Read type first: null is the whole purchase for a discount, no item number for a free product.
     * @return string|null
    */
    public function getProductReference(): ?string {
        return $this->product_reference;
    }

    /**
     * Gets the remaining_stock property value. The remaining_stock property
     * @return int|null
    */
    public function getRemainingStock(): ?int {
        return $this->remaining_stock;
    }

    /**
     * Gets the total_stock property value. The total_stock property
     * @return int|null
    */
    public function getTotalStock(): ?int {
        return $this->total_stock;
    }

    /**
     * Gets the type property value. The type property
     * @return string|null
    */
    public function getType(): ?string {
        return $this->type;
    }

    /**
     * Serializes information the current object
     * @param SerializationWriter $writer Serialization writer to use to serialize this model
    */
    public function serialize(SerializationWriter $writer): void {
        $writer->writeStringValue('available_from', $this->getAvailableFrom());
        $writer->writeStringValue('available_until', $this->getAvailableUntil());
        $writer->writeCollectionOfObjectValues('branches', $this->getBranches());
        $writer->writeIntegerValue('customer_redemptions', $this->getCustomerRedemptions());
        $writer->writeStringValue('description', $this->getDescription());
        $writer->writeEnumValue('discount_type', $this->getDiscountType());
        $writer->writeIntegerValue('discount_value', $this->getDiscountValue());
        $writer->writeIntegerValue('id', $this->getId());
        $writer->writeStringValue('image_url', $this->getImageUrl());
        $writer->writeBooleanValue('is_unlimited', $this->getIsUnlimited());
        $writer->writeIntegerValue('max_redemptions_per_customer', $this->getMaxRedemptionsPerCustomer());
        $writer->writeStringValue('name', $this->getName());
        $writer->writeIntegerValue('payment_amount', $this->getPaymentAmount());
        $writer->writeIntegerValue('point_cost', $this->getPointCost());
        $writer->writeStringValue('product_reference', $this->getProductReference());
        $writer->writeIntegerValue('remaining_stock', $this->getRemainingStock());
        $writer->writeIntegerValue('total_stock', $this->getTotalStock());
        $writer->writeStringValue('type', $this->getType());
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
     * Sets the available_from property value. The available_from property
     * @param string|null $value Value to set for the available_from property.
    */
    public function setAvailableFrom(?string $value): void {
        $this->available_from = $value;
    }

    /**
     * Sets the available_until property value. The available_until property
     * @param string|null $value Value to set for the available_until property.
    */
    public function setAvailableUntil(?string $value): void {
        $this->available_until = $value;
    }

    /**
     * Sets the branches property value. null means every branch
     * @param array<BranchReferenceData>|null $value Value to set for the branches property.
    */
    public function setBranches(?array $value): void {
        $this->branches = $value;
    }

    /**
     * Sets the customer_redemptions property value. How many times the customer named by identifier redeemed this reward, cancelled ones left out; null when no identifier was sent or it matched nobody.
     * @param int|null $value Value to set for the customer_redemptions property.
    */
    public function setCustomerRedemptions(?int $value): void {
        $this->customer_redemptions = $value;
    }

    /**
     * Sets the description property value. The description property
     * @param string|null $value Value to set for the description property.
    */
    public function setDescription(?string $value): void {
        $this->description = $value;
    }

    /**
     * Sets the discount_type property value. `percentage` or `fixed_amount` for a discount, null for a free product.
     * @param RewardCatalogItemData_discount_type|null $value Value to set for the discount_type property.
    */
    public function setDiscountType(?RewardCatalogItemData_discount_type $value): void {
        $this->discount_type = $value;
    }

    /**
     * Sets the discount_value property value. A percentage as a whole number (10 is 10%), a fixed amount in cents; null for a free product.
     * @param int|null $value Value to set for the discount_value property.
    */
    public function setDiscountValue(?int $value): void {
        $this->discount_value = $value;
    }

    /**
     * Sets the id property value. The id property
     * @param int|null $value Value to set for the id property.
    */
    public function setId(?int $value): void {
        $this->id = $value;
    }

    /**
     * Sets the image_url property value. The image_url property
     * @param string|null $value Value to set for the image_url property.
    */
    public function setImageUrl(?string $value): void {
        $this->image_url = $value;
    }

    /**
     * Sets the is_unlimited property value. True when the reward has no stock limit; remaining_stock is then 0.
     * @param bool|null $value Value to set for the is_unlimited property.
    */
    public function setIsUnlimited(?bool $value): void {
        $this->is_unlimited = $value;
    }

    /**
     * Sets the max_redemptions_per_customer property value. How many times one customer can redeem this reward; null means no limit.
     * @param int|null $value Value to set for the max_redemptions_per_customer property.
    */
    public function setMaxRedemptionsPerCustomer(?int $value): void {
        $this->max_redemptions_per_customer = $value;
    }

    /**
     * Sets the name property value. The name property
     * @param string|null $value Value to set for the name property.
    */
    public function setName(?string $value): void {
        $this->name = $value;
    }

    /**
     * Sets the payment_amount property value. The payment_amount property
     * @param int|null $value Value to set for the payment_amount property.
    */
    public function setPaymentAmount(?int $value): void {
        $this->payment_amount = $value;
    }

    /**
     * Sets the point_cost property value. The point_cost property
     * @param int|null $value Value to set for the point_cost property.
    */
    public function setPointCost(?int $value): void {
        $this->point_cost = $value;
    }

    /**
     * Sets the product_reference property value. The item number the reward is for. Read type first: null is the whole purchase for a discount, no item number for a free product.
     * @param string|null $value Value to set for the product_reference property.
    */
    public function setProductReference(?string $value): void {
        $this->product_reference = $value;
    }

    /**
     * Sets the remaining_stock property value. The remaining_stock property
     * @param int|null $value Value to set for the remaining_stock property.
    */
    public function setRemainingStock(?int $value): void {
        $this->remaining_stock = $value;
    }

    /**
     * Sets the total_stock property value. The total_stock property
     * @param int|null $value Value to set for the total_stock property.
    */
    public function setTotalStock(?int $value): void {
        $this->total_stock = $value;
    }

    /**
     * Sets the type property value. The type property
     * @param string|null $value Value to set for the type property.
    */
    public function setType(?string $value): void {
        $this->type = $value;
    }

}
