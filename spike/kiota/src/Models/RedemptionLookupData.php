<?php

namespace Puntjes\Spike\Kiota\Models;

use Microsoft\Kiota\Abstractions\Serialization\AdditionalDataHolder;
use Microsoft\Kiota\Abstractions\Serialization\Parsable;
use Microsoft\Kiota\Abstractions\Serialization\ParseNode;
use Microsoft\Kiota\Abstractions\Serialization\SerializationWriter;

class RedemptionLookupData implements AdditionalDataHolder, Parsable 
{
    /**
     * @var array<string, mixed>|null $additionalData Stores additional data not described in the OpenAPI description found when deserializing. Can be used for serialization as well.
    */
    private ?array $additionalData = null;
    
    /**
     * @var string|null $confirmation_code The confirmation_code property
    */
    private ?string $confirmation_code = null;
    
    /**
     * @var RedemptionCustomerData|null $customer The customer property
    */
    private ?RedemptionCustomerData $customer = null;
    
    /**
     * @var string|null $expires_at The expires_at property
    */
    private ?string $expires_at = null;
    
    /**
     * @var int|null $points_deducted The points_deducted property
    */
    private ?int $points_deducted = null;
    
    /**
     * @var string|null $redeemed_at The redeemed_at property
    */
    private ?string $redeemed_at = null;
    
    /**
     * @var int|null $redemption_id The redemption_id property
    */
    private ?int $redemption_id = null;
    
    /**
     * @var RedemptionRewardData|null $reward The reward property
    */
    private ?RedemptionRewardData $reward = null;
    
    /**
     * @var string|null $status The status property
    */
    private ?string $status = null;
    
    /**
     * @var RedemptionLookupData_type_specific_data|null $type_specific_data What the counter needs, shaped by the reward's type. Its `type` says which shape this is.
    */
    private ?RedemptionLookupData_type_specific_data $type_specific_data = null;
    
    /**
     * @var string|null $verified_at The verified_at property
    */
    private ?string $verified_at = null;
    
    /**
     * Instantiates a new RedemptionLookupData and sets the default values.
    */
    public function __construct() {
        $this->setAdditionalData([]);
    }

    /**
     * Creates a new instance of the appropriate class based on discriminator value
     * @param ParseNode $parseNode The parse node to use to read the discriminator value and create the object
     * @return RedemptionLookupData
    */
    public static function createFromDiscriminatorValue(ParseNode $parseNode): RedemptionLookupData {
        return new RedemptionLookupData();
    }

    /**
     * Gets the AdditionalData property value. Stores additional data not described in the OpenAPI description found when deserializing. Can be used for serialization as well.
     * @return array<string, mixed>|null
    */
    public function getAdditionalData(): ?array {
        return $this->additionalData;
    }

    /**
     * Gets the confirmation_code property value. The confirmation_code property
     * @return string|null
    */
    public function getConfirmationCode(): ?string {
        return $this->confirmation_code;
    }

    /**
     * Gets the customer property value. The customer property
     * @return RedemptionCustomerData|null
    */
    public function getCustomer(): ?RedemptionCustomerData {
        return $this->customer;
    }

    /**
     * Gets the expires_at property value. The expires_at property
     * @return string|null
    */
    public function getExpiresAt(): ?string {
        return $this->expires_at;
    }

    /**
     * The deserialization information for the current model
     * @return array<string, callable(ParseNode): void>
    */
    public function getFieldDeserializers(): array {
        $o = $this;
        return  [
            'confirmation_code' => fn(ParseNode $n) => $o->setConfirmationCode($n->getStringValue()),
            'customer' => fn(ParseNode $n) => $o->setCustomer($n->getObjectValue([RedemptionCustomerData::class, 'createFromDiscriminatorValue'])),
            'expires_at' => fn(ParseNode $n) => $o->setExpiresAt($n->getStringValue()),
            'points_deducted' => fn(ParseNode $n) => $o->setPointsDeducted($n->getIntegerValue()),
            'redeemed_at' => fn(ParseNode $n) => $o->setRedeemedAt($n->getStringValue()),
            'redemption_id' => fn(ParseNode $n) => $o->setRedemptionId($n->getIntegerValue()),
            'reward' => fn(ParseNode $n) => $o->setReward($n->getObjectValue([RedemptionRewardData::class, 'createFromDiscriminatorValue'])),
            'status' => fn(ParseNode $n) => $o->setStatus($n->getStringValue()),
            'type_specific_data' => fn(ParseNode $n) => $o->setTypeSpecificData($n->getObjectValue([RedemptionLookupData_type_specific_data::class, 'createFromDiscriminatorValue'])),
            'verified_at' => fn(ParseNode $n) => $o->setVerifiedAt($n->getStringValue()),
        ];
    }

    /**
     * Gets the points_deducted property value. The points_deducted property
     * @return int|null
    */
    public function getPointsDeducted(): ?int {
        return $this->points_deducted;
    }

    /**
     * Gets the redeemed_at property value. The redeemed_at property
     * @return string|null
    */
    public function getRedeemedAt(): ?string {
        return $this->redeemed_at;
    }

    /**
     * Gets the redemption_id property value. The redemption_id property
     * @return int|null
    */
    public function getRedemptionId(): ?int {
        return $this->redemption_id;
    }

    /**
     * Gets the reward property value. The reward property
     * @return RedemptionRewardData|null
    */
    public function getReward(): ?RedemptionRewardData {
        return $this->reward;
    }

    /**
     * Gets the status property value. The status property
     * @return string|null
    */
    public function getStatus(): ?string {
        return $this->status;
    }

    /**
     * Gets the type_specific_data property value. What the counter needs, shaped by the reward's type. Its `type` says which shape this is.
     * @return RedemptionLookupData_type_specific_data|null
    */
    public function getTypeSpecificData(): ?RedemptionLookupData_type_specific_data {
        return $this->type_specific_data;
    }

    /**
     * Gets the verified_at property value. The verified_at property
     * @return string|null
    */
    public function getVerifiedAt(): ?string {
        return $this->verified_at;
    }

    /**
     * Serializes information the current object
     * @param SerializationWriter $writer Serialization writer to use to serialize this model
    */
    public function serialize(SerializationWriter $writer): void {
        $writer->writeStringValue('confirmation_code', $this->getConfirmationCode());
        $writer->writeObjectValue('customer', $this->getCustomer());
        $writer->writeStringValue('expires_at', $this->getExpiresAt());
        $writer->writeIntegerValue('points_deducted', $this->getPointsDeducted());
        $writer->writeStringValue('redeemed_at', $this->getRedeemedAt());
        $writer->writeIntegerValue('redemption_id', $this->getRedemptionId());
        $writer->writeObjectValue('reward', $this->getReward());
        $writer->writeStringValue('status', $this->getStatus());
        $writer->writeObjectValue('type_specific_data', $this->getTypeSpecificData());
        $writer->writeStringValue('verified_at', $this->getVerifiedAt());
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
     * Sets the confirmation_code property value. The confirmation_code property
     * @param string|null $value Value to set for the confirmation_code property.
    */
    public function setConfirmationCode(?string $value): void {
        $this->confirmation_code = $value;
    }

    /**
     * Sets the customer property value. The customer property
     * @param RedemptionCustomerData|null $value Value to set for the customer property.
    */
    public function setCustomer(?RedemptionCustomerData $value): void {
        $this->customer = $value;
    }

    /**
     * Sets the expires_at property value. The expires_at property
     * @param string|null $value Value to set for the expires_at property.
    */
    public function setExpiresAt(?string $value): void {
        $this->expires_at = $value;
    }

    /**
     * Sets the points_deducted property value. The points_deducted property
     * @param int|null $value Value to set for the points_deducted property.
    */
    public function setPointsDeducted(?int $value): void {
        $this->points_deducted = $value;
    }

    /**
     * Sets the redeemed_at property value. The redeemed_at property
     * @param string|null $value Value to set for the redeemed_at property.
    */
    public function setRedeemedAt(?string $value): void {
        $this->redeemed_at = $value;
    }

    /**
     * Sets the redemption_id property value. The redemption_id property
     * @param int|null $value Value to set for the redemption_id property.
    */
    public function setRedemptionId(?int $value): void {
        $this->redemption_id = $value;
    }

    /**
     * Sets the reward property value. The reward property
     * @param RedemptionRewardData|null $value Value to set for the reward property.
    */
    public function setReward(?RedemptionRewardData $value): void {
        $this->reward = $value;
    }

    /**
     * Sets the status property value. The status property
     * @param string|null $value Value to set for the status property.
    */
    public function setStatus(?string $value): void {
        $this->status = $value;
    }

    /**
     * Sets the type_specific_data property value. What the counter needs, shaped by the reward's type. Its `type` says which shape this is.
     * @param RedemptionLookupData_type_specific_data|null $value Value to set for the type_specific_data property.
    */
    public function setTypeSpecificData(?RedemptionLookupData_type_specific_data $value): void {
        $this->type_specific_data = $value;
    }

    /**
     * Sets the verified_at property value. The verified_at property
     * @param string|null $value Value to set for the verified_at property.
    */
    public function setVerifiedAt(?string $value): void {
        $this->verified_at = $value;
    }

}
