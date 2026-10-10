<?php

namespace Puntjes\Spike\Kiota\Models;

use Microsoft\Kiota\Abstractions\Serialization\AdditionalDataHolder;
use Microsoft\Kiota\Abstractions\Serialization\Parsable;
use Microsoft\Kiota\Abstractions\Serialization\ParseNode;
use Microsoft\Kiota\Abstractions\Serialization\SerializationWriter;

class RedemptionCreatedData implements AdditionalDataHolder, Parsable 
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
     * @var int|null $remaining_balance The remaining_balance property
    */
    private ?int $remaining_balance = null;
    
    /**
     * @var RedemptionRewardData|null $reward The reward property
    */
    private ?RedemptionRewardData $reward = null;
    
    /**
     * @var RedemptionCreatedData_type_specific_data|null $type_specific_data What the counter needs, shaped by the reward's type. Its `type` says which shape this is.
    */
    private ?RedemptionCreatedData_type_specific_data $type_specific_data = null;
    
    /**
     * Instantiates a new RedemptionCreatedData and sets the default values.
    */
    public function __construct() {
        $this->setAdditionalData([]);
    }

    /**
     * Creates a new instance of the appropriate class based on discriminator value
     * @param ParseNode $parseNode The parse node to use to read the discriminator value and create the object
     * @return RedemptionCreatedData
    */
    public static function createFromDiscriminatorValue(ParseNode $parseNode): RedemptionCreatedData {
        return new RedemptionCreatedData();
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
            'expires_at' => fn(ParseNode $n) => $o->setExpiresAt($n->getStringValue()),
            'points_deducted' => fn(ParseNode $n) => $o->setPointsDeducted($n->getIntegerValue()),
            'redeemed_at' => fn(ParseNode $n) => $o->setRedeemedAt($n->getStringValue()),
            'redemption_id' => fn(ParseNode $n) => $o->setRedemptionId($n->getIntegerValue()),
            'remaining_balance' => fn(ParseNode $n) => $o->setRemainingBalance($n->getIntegerValue()),
            'reward' => fn(ParseNode $n) => $o->setReward($n->getObjectValue([RedemptionRewardData::class, 'createFromDiscriminatorValue'])),
            'type_specific_data' => fn(ParseNode $n) => $o->setTypeSpecificData($n->getObjectValue([RedemptionCreatedData_type_specific_data::class, 'createFromDiscriminatorValue'])),
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
     * Gets the remaining_balance property value. The remaining_balance property
     * @return int|null
    */
    public function getRemainingBalance(): ?int {
        return $this->remaining_balance;
    }

    /**
     * Gets the reward property value. The reward property
     * @return RedemptionRewardData|null
    */
    public function getReward(): ?RedemptionRewardData {
        return $this->reward;
    }

    /**
     * Gets the type_specific_data property value. What the counter needs, shaped by the reward's type. Its `type` says which shape this is.
     * @return RedemptionCreatedData_type_specific_data|null
    */
    public function getTypeSpecificData(): ?RedemptionCreatedData_type_specific_data {
        return $this->type_specific_data;
    }

    /**
     * Serializes information the current object
     * @param SerializationWriter $writer Serialization writer to use to serialize this model
    */
    public function serialize(SerializationWriter $writer): void {
        $writer->writeStringValue('confirmation_code', $this->getConfirmationCode());
        $writer->writeStringValue('expires_at', $this->getExpiresAt());
        $writer->writeIntegerValue('points_deducted', $this->getPointsDeducted());
        $writer->writeStringValue('redeemed_at', $this->getRedeemedAt());
        $writer->writeIntegerValue('redemption_id', $this->getRedemptionId());
        $writer->writeIntegerValue('remaining_balance', $this->getRemainingBalance());
        $writer->writeObjectValue('reward', $this->getReward());
        $writer->writeObjectValue('type_specific_data', $this->getTypeSpecificData());
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
     * Sets the remaining_balance property value. The remaining_balance property
     * @param int|null $value Value to set for the remaining_balance property.
    */
    public function setRemainingBalance(?int $value): void {
        $this->remaining_balance = $value;
    }

    /**
     * Sets the reward property value. The reward property
     * @param RedemptionRewardData|null $value Value to set for the reward property.
    */
    public function setReward(?RedemptionRewardData $value): void {
        $this->reward = $value;
    }

    /**
     * Sets the type_specific_data property value. What the counter needs, shaped by the reward's type. Its `type` says which shape this is.
     * @param RedemptionCreatedData_type_specific_data|null $value Value to set for the type_specific_data property.
    */
    public function setTypeSpecificData(?RedemptionCreatedData_type_specific_data $value): void {
        $this->type_specific_data = $value;
    }

}
