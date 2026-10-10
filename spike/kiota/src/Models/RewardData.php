<?php

namespace Puntjes\Spike\Kiota\Models;

use Microsoft\Kiota\Abstractions\Serialization\AdditionalDataHolder;
use Microsoft\Kiota\Abstractions\Serialization\Parsable;
use Microsoft\Kiota\Abstractions\Serialization\ParseNode;
use Microsoft\Kiota\Abstractions\Serialization\SerializationWriter;

class RewardData implements AdditionalDataHolder, Parsable 
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
     * @var int|null $code_valid_for_hours The code_valid_for_hours property
    */
    private ?int $code_valid_for_hours = null;
    
    /**
     * @var string|null $created_at The created_at property
    */
    private ?string $created_at = null;
    
    /**
     * @var string|null $description The description property
    */
    private ?string $description = null;
    
    /**
     * @var string|null $discount_type The discount_type property
    */
    private ?string $discount_type = null;
    
    /**
     * @var int|null $discount_value The discount_value property
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
     * @var int|null $max_redemptions_per_customer The max_redemptions_per_customer property
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
     * @var int|null $product_id The product_id property
    */
    private ?int $product_id = null;
    
    /**
     * @var string|null $product_reference The product_reference property
    */
    private ?string $product_reference = null;
    
    /**
     * @var int|null $remaining_stock The remaining_stock property
    */
    private ?int $remaining_stock = null;
    
    /**
     * @var RewardStatusData|null $status The status property
    */
    private ?RewardStatusData $status = null;
    
    /**
     * @var int|null $total_stock The total_stock property
    */
    private ?int $total_stock = null;
    
    /**
     * @var string|null $type The type property
    */
    private ?string $type = null;
    
    /**
     * @var string|null $updated_at The updated_at property
    */
    private ?string $updated_at = null;
    
    /**
     * Instantiates a new RewardData and sets the default values.
    */
    public function __construct() {
        $this->setAdditionalData([]);
    }

    /**
     * Creates a new instance of the appropriate class based on discriminator value
     * @param ParseNode $parseNode The parse node to use to read the discriminator value and create the object
     * @return RewardData
    */
    public static function createFromDiscriminatorValue(ParseNode $parseNode): RewardData {
        return new RewardData();
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
     * Gets the code_valid_for_hours property value. The code_valid_for_hours property
     * @return int|null
    */
    public function getCodeValidForHours(): ?int {
        return $this->code_valid_for_hours;
    }

    /**
     * Gets the created_at property value. The created_at property
     * @return string|null
    */
    public function getCreatedAt(): ?string {
        return $this->created_at;
    }

    /**
     * Gets the description property value. The description property
     * @return string|null
    */
    public function getDescription(): ?string {
        return $this->description;
    }

    /**
     * Gets the discount_type property value. The discount_type property
     * @return string|null
    */
    public function getDiscountType(): ?string {
        return $this->discount_type;
    }

    /**
     * Gets the discount_value property value. The discount_value property
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
            'code_valid_for_hours' => fn(ParseNode $n) => $o->setCodeValidForHours($n->getIntegerValue()),
            'created_at' => fn(ParseNode $n) => $o->setCreatedAt($n->getStringValue()),
            'description' => fn(ParseNode $n) => $o->setDescription($n->getStringValue()),
            'discount_type' => fn(ParseNode $n) => $o->setDiscountType($n->getStringValue()),
            'discount_value' => fn(ParseNode $n) => $o->setDiscountValue($n->getIntegerValue()),
            'id' => fn(ParseNode $n) => $o->setId($n->getIntegerValue()),
            'image_url' => fn(ParseNode $n) => $o->setImageUrl($n->getStringValue()),
            'is_unlimited' => fn(ParseNode $n) => $o->setIsUnlimited($n->getBooleanValue()),
            'max_redemptions_per_customer' => fn(ParseNode $n) => $o->setMaxRedemptionsPerCustomer($n->getIntegerValue()),
            'name' => fn(ParseNode $n) => $o->setName($n->getStringValue()),
            'payment_amount' => fn(ParseNode $n) => $o->setPaymentAmount($n->getIntegerValue()),
            'point_cost' => fn(ParseNode $n) => $o->setPointCost($n->getIntegerValue()),
            'product_id' => fn(ParseNode $n) => $o->setProductId($n->getIntegerValue()),
            'product_reference' => fn(ParseNode $n) => $o->setProductReference($n->getStringValue()),
            'remaining_stock' => fn(ParseNode $n) => $o->setRemainingStock($n->getIntegerValue()),
            'status' => fn(ParseNode $n) => $o->setStatus($n->getObjectValue([RewardStatusData::class, 'createFromDiscriminatorValue'])),
            'total_stock' => fn(ParseNode $n) => $o->setTotalStock($n->getIntegerValue()),
            'type' => fn(ParseNode $n) => $o->setType($n->getStringValue()),
            'updated_at' => fn(ParseNode $n) => $o->setUpdatedAt($n->getStringValue()),
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
     * Gets the max_redemptions_per_customer property value. The max_redemptions_per_customer property
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
     * Gets the product_id property value. The product_id property
     * @return int|null
    */
    public function getProductId(): ?int {
        return $this->product_id;
    }

    /**
     * Gets the product_reference property value. The product_reference property
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
     * Gets the status property value. The status property
     * @return RewardStatusData|null
    */
    public function getStatus(): ?RewardStatusData {
        return $this->status;
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
     * Gets the updated_at property value. The updated_at property
     * @return string|null
    */
    public function getUpdatedAt(): ?string {
        return $this->updated_at;
    }

    /**
     * Serializes information the current object
     * @param SerializationWriter $writer Serialization writer to use to serialize this model
    */
    public function serialize(SerializationWriter $writer): void {
        $writer->writeStringValue('available_from', $this->getAvailableFrom());
        $writer->writeStringValue('available_until', $this->getAvailableUntil());
        $writer->writeIntegerValue('code_valid_for_hours', $this->getCodeValidForHours());
        $writer->writeStringValue('created_at', $this->getCreatedAt());
        $writer->writeStringValue('description', $this->getDescription());
        $writer->writeStringValue('discount_type', $this->getDiscountType());
        $writer->writeIntegerValue('discount_value', $this->getDiscountValue());
        $writer->writeIntegerValue('id', $this->getId());
        $writer->writeStringValue('image_url', $this->getImageUrl());
        $writer->writeBooleanValue('is_unlimited', $this->getIsUnlimited());
        $writer->writeIntegerValue('max_redemptions_per_customer', $this->getMaxRedemptionsPerCustomer());
        $writer->writeStringValue('name', $this->getName());
        $writer->writeIntegerValue('payment_amount', $this->getPaymentAmount());
        $writer->writeIntegerValue('point_cost', $this->getPointCost());
        $writer->writeIntegerValue('product_id', $this->getProductId());
        $writer->writeStringValue('product_reference', $this->getProductReference());
        $writer->writeIntegerValue('remaining_stock', $this->getRemainingStock());
        $writer->writeObjectValue('status', $this->getStatus());
        $writer->writeIntegerValue('total_stock', $this->getTotalStock());
        $writer->writeStringValue('type', $this->getType());
        $writer->writeStringValue('updated_at', $this->getUpdatedAt());
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
     * Sets the code_valid_for_hours property value. The code_valid_for_hours property
     * @param int|null $value Value to set for the code_valid_for_hours property.
    */
    public function setCodeValidForHours(?int $value): void {
        $this->code_valid_for_hours = $value;
    }

    /**
     * Sets the created_at property value. The created_at property
     * @param string|null $value Value to set for the created_at property.
    */
    public function setCreatedAt(?string $value): void {
        $this->created_at = $value;
    }

    /**
     * Sets the description property value. The description property
     * @param string|null $value Value to set for the description property.
    */
    public function setDescription(?string $value): void {
        $this->description = $value;
    }

    /**
     * Sets the discount_type property value. The discount_type property
     * @param string|null $value Value to set for the discount_type property.
    */
    public function setDiscountType(?string $value): void {
        $this->discount_type = $value;
    }

    /**
     * Sets the discount_value property value. The discount_value property
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
     * Sets the max_redemptions_per_customer property value. The max_redemptions_per_customer property
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
     * Sets the product_id property value. The product_id property
     * @param int|null $value Value to set for the product_id property.
    */
    public function setProductId(?int $value): void {
        $this->product_id = $value;
    }

    /**
     * Sets the product_reference property value. The product_reference property
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
     * Sets the status property value. The status property
     * @param RewardStatusData|null $value Value to set for the status property.
    */
    public function setStatus(?RewardStatusData $value): void {
        $this->status = $value;
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

    /**
     * Sets the updated_at property value. The updated_at property
     * @param string|null $value Value to set for the updated_at property.
    */
    public function setUpdatedAt(?string $value): void {
        $this->updated_at = $value;
    }

}
