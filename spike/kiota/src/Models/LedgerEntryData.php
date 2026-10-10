<?php

namespace Puntjes\Spike\Kiota\Models;

use Microsoft\Kiota\Abstractions\Serialization\AdditionalDataHolder;
use Microsoft\Kiota\Abstractions\Serialization\Parsable;
use Microsoft\Kiota\Abstractions\Serialization\ParseNode;
use Microsoft\Kiota\Abstractions\Serialization\SerializationWriter;

class LedgerEntryData implements AdditionalDataHolder, Parsable 
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
     * @var string|null $causer_id The causer_id property
    */
    private ?string $causer_id = null;
    
    /**
     * @var string|null $causer_type The causer_type property
    */
    private ?string $causer_type = null;
    
    /**
     * @var string|null $created_at The created_at property
    */
    private ?string $created_at = null;
    
    /**
     * @var int|null $id The id property
    */
    private ?int $id = null;
    
    /**
     * @var string|null $reason The reason property
    */
    private ?string $reason = null;
    
    /**
     * @var int|null $running_balance The running_balance property
    */
    private ?int $running_balance = null;
    
    /**
     * @var string|null $type The type property
    */
    private ?string $type = null;
    
    /**
     * @var int|null $wallet_id The wallet_id property
    */
    private ?int $wallet_id = null;
    
    /**
     * Instantiates a new LedgerEntryData and sets the default values.
    */
    public function __construct() {
        $this->setAdditionalData([]);
    }

    /**
     * Creates a new instance of the appropriate class based on discriminator value
     * @param ParseNode $parseNode The parse node to use to read the discriminator value and create the object
     * @return LedgerEntryData
    */
    public static function createFromDiscriminatorValue(ParseNode $parseNode): LedgerEntryData {
        return new LedgerEntryData();
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
     * Gets the causer_id property value. The causer_id property
     * @return string|null
    */
    public function getCauserId(): ?string {
        return $this->causer_id;
    }

    /**
     * Gets the causer_type property value. The causer_type property
     * @return string|null
    */
    public function getCauserType(): ?string {
        return $this->causer_type;
    }

    /**
     * Gets the created_at property value. The created_at property
     * @return string|null
    */
    public function getCreatedAt(): ?string {
        return $this->created_at;
    }

    /**
     * The deserialization information for the current model
     * @return array<string, callable(ParseNode): void>
    */
    public function getFieldDeserializers(): array {
        $o = $this;
        return  [
            'amount' => fn(ParseNode $n) => $o->setAmount($n->getIntegerValue()),
            'causer_id' => fn(ParseNode $n) => $o->setCauserId($n->getStringValue()),
            'causer_type' => fn(ParseNode $n) => $o->setCauserType($n->getStringValue()),
            'created_at' => fn(ParseNode $n) => $o->setCreatedAt($n->getStringValue()),
            'id' => fn(ParseNode $n) => $o->setId($n->getIntegerValue()),
            'reason' => fn(ParseNode $n) => $o->setReason($n->getStringValue()),
            'running_balance' => fn(ParseNode $n) => $o->setRunningBalance($n->getIntegerValue()),
            'type' => fn(ParseNode $n) => $o->setType($n->getStringValue()),
            'wallet_id' => fn(ParseNode $n) => $o->setWalletId($n->getIntegerValue()),
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
     * Gets the reason property value. The reason property
     * @return string|null
    */
    public function getReason(): ?string {
        return $this->reason;
    }

    /**
     * Gets the running_balance property value. The running_balance property
     * @return int|null
    */
    public function getRunningBalance(): ?int {
        return $this->running_balance;
    }

    /**
     * Gets the type property value. The type property
     * @return string|null
    */
    public function getType(): ?string {
        return $this->type;
    }

    /**
     * Gets the wallet_id property value. The wallet_id property
     * @return int|null
    */
    public function getWalletId(): ?int {
        return $this->wallet_id;
    }

    /**
     * Serializes information the current object
     * @param SerializationWriter $writer Serialization writer to use to serialize this model
    */
    public function serialize(SerializationWriter $writer): void {
        $writer->writeIntegerValue('amount', $this->getAmount());
        $writer->writeStringValue('causer_id', $this->getCauserId());
        $writer->writeStringValue('causer_type', $this->getCauserType());
        $writer->writeStringValue('created_at', $this->getCreatedAt());
        $writer->writeIntegerValue('id', $this->getId());
        $writer->writeStringValue('reason', $this->getReason());
        $writer->writeIntegerValue('running_balance', $this->getRunningBalance());
        $writer->writeStringValue('type', $this->getType());
        $writer->writeIntegerValue('wallet_id', $this->getWalletId());
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
     * Sets the causer_id property value. The causer_id property
     * @param string|null $value Value to set for the causer_id property.
    */
    public function setCauserId(?string $value): void {
        $this->causer_id = $value;
    }

    /**
     * Sets the causer_type property value. The causer_type property
     * @param string|null $value Value to set for the causer_type property.
    */
    public function setCauserType(?string $value): void {
        $this->causer_type = $value;
    }

    /**
     * Sets the created_at property value. The created_at property
     * @param string|null $value Value to set for the created_at property.
    */
    public function setCreatedAt(?string $value): void {
        $this->created_at = $value;
    }

    /**
     * Sets the id property value. The id property
     * @param int|null $value Value to set for the id property.
    */
    public function setId(?int $value): void {
        $this->id = $value;
    }

    /**
     * Sets the reason property value. The reason property
     * @param string|null $value Value to set for the reason property.
    */
    public function setReason(?string $value): void {
        $this->reason = $value;
    }

    /**
     * Sets the running_balance property value. The running_balance property
     * @param int|null $value Value to set for the running_balance property.
    */
    public function setRunningBalance(?int $value): void {
        $this->running_balance = $value;
    }

    /**
     * Sets the type property value. The type property
     * @param string|null $value Value to set for the type property.
    */
    public function setType(?string $value): void {
        $this->type = $value;
    }

    /**
     * Sets the wallet_id property value. The wallet_id property
     * @param int|null $value Value to set for the wallet_id property.
    */
    public function setWalletId(?int $value): void {
        $this->wallet_id = $value;
    }

}
