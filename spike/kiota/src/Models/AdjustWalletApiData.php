<?php

namespace Puntjes\Spike\Kiota\Models;

use Microsoft\Kiota\Abstractions\Serialization\AdditionalDataHolder;
use Microsoft\Kiota\Abstractions\Serialization\Parsable;
use Microsoft\Kiota\Abstractions\Serialization\ParseNode;
use Microsoft\Kiota\Abstractions\Serialization\SerializationWriter;

class AdjustWalletApiData implements AdditionalDataHolder, Parsable 
{
    /**
     * @var array<string, mixed>|null $additionalData Stores additional data not described in the OpenAPI description found when deserializing. Can be used for serialization as well.
    */
    private ?array $additionalData = null;
    
    /**
     * @var int|null $amount The amount property
    */
    private ?int $amount = null;
    
    /**
     * @var string|null $idempotency_key The idempotency_key property
    */
    private ?string $idempotency_key = null;
    
    /**
     * @var string|null $reason The reason property
    */
    private ?string $reason = null;
    
    /**
     * Instantiates a new AdjustWalletApiData and sets the default values.
    */
    public function __construct() {
        $this->setAdditionalData([]);
    }

    /**
     * Creates a new instance of the appropriate class based on discriminator value
     * @param ParseNode $parseNode The parse node to use to read the discriminator value and create the object
     * @return AdjustWalletApiData
    */
    public static function createFromDiscriminatorValue(ParseNode $parseNode): AdjustWalletApiData {
        return new AdjustWalletApiData();
    }

    /**
     * Gets the AdditionalData property value. Stores additional data not described in the OpenAPI description found when deserializing. Can be used for serialization as well.
     * @return array<string, mixed>|null
    */
    public function getAdditionalData(): ?array {
        return $this->additionalData;
    }

    /**
     * Gets the amount property value. The amount property
     * @return int|null
    */
    public function getAmount(): ?int {
        return $this->amount;
    }

    /**
     * The deserialization information for the current model
     * @return array<string, callable(ParseNode): void>
    */
    public function getFieldDeserializers(): array {
        $o = $this;
        return  [
            'amount' => fn(ParseNode $n) => $o->setAmount($n->getIntegerValue()),
            'idempotency_key' => fn(ParseNode $n) => $o->setIdempotencyKey($n->getStringValue()),
            'reason' => fn(ParseNode $n) => $o->setReason($n->getStringValue()),
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
     * Gets the reason property value. The reason property
     * @return string|null
    */
    public function getReason(): ?string {
        return $this->reason;
    }

    /**
     * Serializes information the current object
     * @param SerializationWriter $writer Serialization writer to use to serialize this model
    */
    public function serialize(SerializationWriter $writer): void {
        $writer->writeIntegerValue('amount', $this->getAmount());
        $writer->writeStringValue('idempotency_key', $this->getIdempotencyKey());
        $writer->writeStringValue('reason', $this->getReason());
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
     * Sets the amount property value. The amount property
     * @param int|null $value Value to set for the amount property.
    */
    public function setAmount(?int $value): void {
        $this->amount = $value;
    }

    /**
     * Sets the idempotency_key property value. The idempotency_key property
     * @param string|null $value Value to set for the idempotency_key property.
    */
    public function setIdempotencyKey(?string $value): void {
        $this->idempotency_key = $value;
    }

    /**
     * Sets the reason property value. The reason property
     * @param string|null $value Value to set for the reason property.
    */
    public function setReason(?string $value): void {
        $this->reason = $value;
    }

}
