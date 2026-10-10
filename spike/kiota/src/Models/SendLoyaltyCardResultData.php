<?php

namespace Puntjes\Spike\Kiota\Models;

use Microsoft\Kiota\Abstractions\Serialization\AdditionalDataHolder;
use Microsoft\Kiota\Abstractions\Serialization\Parsable;
use Microsoft\Kiota\Abstractions\Serialization\ParseNode;
use Microsoft\Kiota\Abstractions\Serialization\SerializationWriter;

class SendLoyaltyCardResultData implements AdditionalDataHolder, Parsable 
{
    /**
     * @var array<string, mixed>|null $additionalData Stores additional data not described in the OpenAPI description found when deserializing. Can be used for serialization as well.
    */
    private ?array $additionalData = null;
    
    /**
     * @var string|null $channel The channel property
    */
    private ?string $channel = null;
    
    /**
     * @var int|null $customer_id The customer_id property
    */
    private ?int $customer_id = null;
    
    /**
     * @var bool|null $queued The queued property
    */
    private ?bool $queued = null;
    
    /**
     * Instantiates a new SendLoyaltyCardResultData and sets the default values.
    */
    public function __construct() {
        $this->setAdditionalData([]);
    }

    /**
     * Creates a new instance of the appropriate class based on discriminator value
     * @param ParseNode $parseNode The parse node to use to read the discriminator value and create the object
     * @return SendLoyaltyCardResultData
    */
    public static function createFromDiscriminatorValue(ParseNode $parseNode): SendLoyaltyCardResultData {
        return new SendLoyaltyCardResultData();
    }

    /**
     * Gets the AdditionalData property value. Stores additional data not described in the OpenAPI description found when deserializing. Can be used for serialization as well.
     * @return array<string, mixed>|null
    */
    public function getAdditionalData(): ?array {
        return $this->additionalData;
    }

    /**
     * Gets the channel property value. The channel property
     * @return string|null
    */
    public function getChannel(): ?string {
        return $this->channel;
    }

    /**
     * Gets the customer_id property value. The customer_id property
     * @return int|null
    */
    public function getCustomerId(): ?int {
        return $this->customer_id;
    }

    /**
     * The deserialization information for the current model
     * @return array<string, callable(ParseNode): void>
    */
    public function getFieldDeserializers(): array {
        $o = $this;
        return  [
            'channel' => fn(ParseNode $n) => $o->setChannel($n->getStringValue()),
            'customer_id' => fn(ParseNode $n) => $o->setCustomerId($n->getIntegerValue()),
            'queued' => fn(ParseNode $n) => $o->setQueued($n->getBooleanValue()),
        ];
    }

    /**
     * Gets the queued property value. The queued property
     * @return bool|null
    */
    public function getQueued(): ?bool {
        return $this->queued;
    }

    /**
     * Serializes information the current object
     * @param SerializationWriter $writer Serialization writer to use to serialize this model
    */
    public function serialize(SerializationWriter $writer): void {
        $writer->writeStringValue('channel', $this->getChannel());
        $writer->writeIntegerValue('customer_id', $this->getCustomerId());
        $writer->writeBooleanValue('queued', $this->getQueued());
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
     * Sets the channel property value. The channel property
     * @param string|null $value Value to set for the channel property.
    */
    public function setChannel(?string $value): void {
        $this->channel = $value;
    }

    /**
     * Sets the customer_id property value. The customer_id property
     * @param int|null $value Value to set for the customer_id property.
    */
    public function setCustomerId(?int $value): void {
        $this->customer_id = $value;
    }

    /**
     * Sets the queued property value. The queued property
     * @param bool|null $value Value to set for the queued property.
    */
    public function setQueued(?bool $value): void {
        $this->queued = $value;
    }

}
