<?php

namespace Puntjes\Spike\Kiota\Models;

use Microsoft\Kiota\Abstractions\Serialization\AdditionalDataHolder;
use Microsoft\Kiota\Abstractions\Serialization\Parsable;
use Microsoft\Kiota\Abstractions\Serialization\ParseNode;
use Microsoft\Kiota\Abstractions\Serialization\SerializationWriter;

class RedemptionVerificationData implements AdditionalDataHolder, Parsable 
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
     * @var int|null $points_deducted The points_deducted property
    */
    private ?int $points_deducted = null;
    
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
     * @var RedemptionVerificationData_type_specific_data|null $type_specific_data What the counter needs, shaped by the reward's type. Its `type` says which shape this is.
    */
    private ?RedemptionVerificationData_type_specific_data $type_specific_data = null;
    
    /**
     * @var string|null $verified_at The verified_at property
    */
    private ?string $verified_at = null;
    
    /**
     * Instantiates a new RedemptionVerificationData and sets the default values.
    */
    public function __construct() {
        $this->setAdditionalData([]);
    }

    /**
     * Creates a new instance of the appropriate class based on discriminator value
     * @param ParseNode $parseNode The parse node to use to read the discriminator value and create the object
     * @return RedemptionVerificationData
    */
    public static function createFromDiscriminatorValue(ParseNode $parseNode): RedemptionVerificationData {
        return new RedemptionVerificationData();
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
     * The deserialization information for the current model
     * @return array<string, callable(ParseNode): void>
    */
    public function getFieldDeserializers(): array {
        $o = $this;
        return  [
            'confirmation_code' => fn(ParseNode $n) => $o->setConfirmationCode($n->getStringValue()),
            'points_deducted' => fn(ParseNode $n) => $o->setPointsDeducted($n->getIntegerValue()),
            'redemption_id' => fn(ParseNode $n) => $o->setRedemptionId($n->getIntegerValue()),
            'reward' => fn(ParseNode $n) => $o->setReward($n->getObjectValue([RedemptionRewardData::class, 'createFromDiscriminatorValue'])),
            'status' => fn(ParseNode $n) => $o->setStatus($n->getStringValue()),
            'type_specific_data' => fn(ParseNode $n) => $o->setTypeSpecificData($n->getObjectValue([RedemptionVerificationData_type_specific_data::class, 'createFromDiscriminatorValue'])),
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
     * @return RedemptionVerificationData_type_specific_data|null
    */
    public function getTypeSpecificData(): ?RedemptionVerificationData_type_specific_data {
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
        $writer->writeIntegerValue('points_deducted', $this->getPointsDeducted());
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
     * Sets the points_deducted property value. The points_deducted property
     * @param int|null $value Value to set for the points_deducted property.
    */
    public function setPointsDeducted(?int $value): void {
        $this->points_deducted = $value;
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
     * @param RedemptionVerificationData_type_specific_data|null $value Value to set for the type_specific_data property.
    */
    public function setTypeSpecificData(?RedemptionVerificationData_type_specific_data $value): void {
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
