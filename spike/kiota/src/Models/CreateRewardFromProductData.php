<?php

namespace Puntjes\Spike\Kiota\Models;

use Microsoft\Kiota\Abstractions\Serialization\AdditionalDataHolder;
use Microsoft\Kiota\Abstractions\Serialization\Parsable;
use Microsoft\Kiota\Abstractions\Serialization\ParseNode;
use Microsoft\Kiota\Abstractions\Serialization\SerializationWriter;

class CreateRewardFromProductData implements AdditionalDataHolder, Parsable 
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
     * @var string|null $description The description property
    */
    private ?string $description = null;
    
    /**
     * @var string|null $idempotency_key The idempotency_key property
    */
    private ?string $idempotency_key = null;
    
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
     * @var CreateRewardFromProductData_status|null $status The status property
    */
    private ?CreateRewardFromProductData_status $status = null;
    
    /**
     * @var int|null $total_stock The total_stock property
    */
    private ?int $total_stock = null;
    
    /**
     * Instantiates a new CreateRewardFromProductData and sets the default values.
    */
    public function __construct() {
        $this->setAdditionalData([]);
    }

    /**
     * Creates a new instance of the appropriate class based on discriminator value
     * @param ParseNode $parseNode The parse node to use to read the discriminator value and create the object
     * @return CreateRewardFromProductData
    */
    public static function createFromDiscriminatorValue(ParseNode $parseNode): CreateRewardFromProductData {
        return new CreateRewardFromProductData();
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
     * Gets the description property value. The description property
     * @return string|null
    */
    public function getDescription(): ?string {
        return $this->description;
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
            'description' => fn(ParseNode $n) => $o->setDescription($n->getStringValue()),
            'idempotency_key' => fn(ParseNode $n) => $o->setIdempotencyKey($n->getStringValue()),
            'max_redemptions_per_customer' => fn(ParseNode $n) => $o->setMaxRedemptionsPerCustomer($n->getIntegerValue()),
            'name' => fn(ParseNode $n) => $o->setName($n->getStringValue()),
            'payment_amount' => fn(ParseNode $n) => $o->setPaymentAmount($n->getIntegerValue()),
            'point_cost' => fn(ParseNode $n) => $o->setPointCost($n->getIntegerValue()),
            'status' => fn(ParseNode $n) => $o->setStatus($n->getEnumValue(CreateRewardFromProductData_status::class)),
            'total_stock' => fn(ParseNode $n) => $o->setTotalStock($n->getIntegerValue()),
        ];
    }

    /**
     * Gets the idempotency_key property value. The idempotency_key property
     * @return string|null
    */
    public function getIdempotencyKey(): ?string {
        return $this->idempotency_key;
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
     * Gets the status property value. The status property
     * @return CreateRewardFromProductData_status|null
    */
    public function getStatus(): ?CreateRewardFromProductData_status {
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
     * Serializes information the current object
     * @param SerializationWriter $writer Serialization writer to use to serialize this model
    */
    public function serialize(SerializationWriter $writer): void {
        $writer->writeStringValue('available_from', $this->getAvailableFrom());
        $writer->writeStringValue('available_until', $this->getAvailableUntil());
        $writer->writeIntegerValue('code_valid_for_hours', $this->getCodeValidForHours());
        $writer->writeStringValue('description', $this->getDescription());
        $writer->writeStringValue('idempotency_key', $this->getIdempotencyKey());
        $writer->writeIntegerValue('max_redemptions_per_customer', $this->getMaxRedemptionsPerCustomer());
        $writer->writeStringValue('name', $this->getName());
        $writer->writeIntegerValue('payment_amount', $this->getPaymentAmount());
        $writer->writeIntegerValue('point_cost', $this->getPointCost());
        $writer->writeEnumValue('status', $this->getStatus());
        $writer->writeIntegerValue('total_stock', $this->getTotalStock());
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
     * Sets the description property value. The description property
     * @param string|null $value Value to set for the description property.
    */
    public function setDescription(?string $value): void {
        $this->description = $value;
    }

    /**
     * Sets the idempotency_key property value. The idempotency_key property
     * @param string|null $value Value to set for the idempotency_key property.
    */
    public function setIdempotencyKey(?string $value): void {
        $this->idempotency_key = $value;
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
     * Sets the status property value. The status property
     * @param CreateRewardFromProductData_status|null $value Value to set for the status property.
    */
    public function setStatus(?CreateRewardFromProductData_status $value): void {
        $this->status = $value;
    }

    /**
     * Sets the total_stock property value. The total_stock property
     * @param int|null $value Value to set for the total_stock property.
    */
    public function setTotalStock(?int $value): void {
        $this->total_stock = $value;
    }

}
