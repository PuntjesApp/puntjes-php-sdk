<?php

namespace Puntjes\Spike\Kiota\Models;

use Microsoft\Kiota\Abstractions\Serialization\AdditionalDataHolder;
use Microsoft\Kiota\Abstractions\Serialization\Parsable;
use Microsoft\Kiota\Abstractions\Serialization\ParseNode;
use Microsoft\Kiota\Abstractions\Serialization\SerializationWriter;

class CreateRedemptionData implements AdditionalDataHolder, Parsable 
{
    /**
     * @var array<string, mixed>|null $additionalData Stores additional data not described in the OpenAPI description found when deserializing. Can be used for serialization as well.
    */
    private ?array $additionalData = null;
    
    /**
     * @var string|null $branch The branch property
    */
    private ?string $branch = null;
    
    /**
     * @var string|null $idempotency_key The idempotency_key property
    */
    private ?string $idempotency_key = null;
    
    /**
     * @var string|null $identifier The identifier property
    */
    private ?string $identifier = null;
    
    /**
     * @var int|null $reward_id The reward_id property
    */
    private ?int $reward_id = null;
    
    /**
     * Instantiates a new CreateRedemptionData and sets the default values.
    */
    public function __construct() {
        $this->setAdditionalData([]);
    }

    /**
     * Creates a new instance of the appropriate class based on discriminator value
     * @param ParseNode $parseNode The parse node to use to read the discriminator value and create the object
     * @return CreateRedemptionData
    */
    public static function createFromDiscriminatorValue(ParseNode $parseNode): CreateRedemptionData {
        return new CreateRedemptionData();
    }

    /**
     * Gets the AdditionalData property value. Stores additional data not described in the OpenAPI description found when deserializing. Can be used for serialization as well.
     * @return array<string, mixed>|null
    */
    public function getAdditionalData(): ?array {
        return $this->additionalData;
    }

    /**
     * Gets the branch property value. The branch property
     * @return string|null
    */
    public function getBranch(): ?string {
        return $this->branch;
    }

    /**
     * The deserialization information for the current model
     * @return array<string, callable(ParseNode): void>
    */
    public function getFieldDeserializers(): array {
        $o = $this;
        return  [
            'branch' => fn(ParseNode $n) => $o->setBranch($n->getStringValue()),
            'idempotency_key' => fn(ParseNode $n) => $o->setIdempotencyKey($n->getStringValue()),
            'identifier' => fn(ParseNode $n) => $o->setIdentifier($n->getStringValue()),
            'reward_id' => fn(ParseNode $n) => $o->setRewardId($n->getIntegerValue()),
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
     * Gets the identifier property value. The identifier property
     * @return string|null
    */
    public function getIdentifier(): ?string {
        return $this->identifier;
    }

    /**
     * Gets the reward_id property value. The reward_id property
     * @return int|null
    */
    public function getRewardId(): ?int {
        return $this->reward_id;
    }

    /**
     * Serializes information the current object
     * @param SerializationWriter $writer Serialization writer to use to serialize this model
    */
    public function serialize(SerializationWriter $writer): void {
        $writer->writeStringValue('branch', $this->getBranch());
        $writer->writeStringValue('idempotency_key', $this->getIdempotencyKey());
        $writer->writeStringValue('identifier', $this->getIdentifier());
        $writer->writeIntegerValue('reward_id', $this->getRewardId());
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
     * Sets the branch property value. The branch property
     * @param string|null $value Value to set for the branch property.
    */
    public function setBranch(?string $value): void {
        $this->branch = $value;
    }

    /**
     * Sets the idempotency_key property value. The idempotency_key property
     * @param string|null $value Value to set for the idempotency_key property.
    */
    public function setIdempotencyKey(?string $value): void {
        $this->idempotency_key = $value;
    }

    /**
     * Sets the identifier property value. The identifier property
     * @param string|null $value Value to set for the identifier property.
    */
    public function setIdentifier(?string $value): void {
        $this->identifier = $value;
    }

    /**
     * Sets the reward_id property value. The reward_id property
     * @param int|null $value Value to set for the reward_id property.
    */
    public function setRewardId(?int $value): void {
        $this->reward_id = $value;
    }

}
