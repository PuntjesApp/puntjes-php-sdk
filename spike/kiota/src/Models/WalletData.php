<?php

namespace Puntjes\Spike\Kiota\Models;

use Microsoft\Kiota\Abstractions\Serialization\AdditionalDataHolder;
use Microsoft\Kiota\Abstractions\Serialization\Parsable;
use Microsoft\Kiota\Abstractions\Serialization\ParseNode;
use Microsoft\Kiota\Abstractions\Serialization\SerializationWriter;

class WalletData implements AdditionalDataHolder, Parsable 
{
    /**
     * @var array<string, mixed>|null $additionalData Stores additional data not described in the OpenAPI description found when deserializing. Can be used for serialization as well.
    */
    private ?array $additionalData = null;
    
    /**
     * @var int|null $balance The balance property
    */
    private ?int $balance = null;
    
    /**
     * @var string|null $created_at The created_at property
    */
    private ?string $created_at = null;
    
    /**
     * @var int|null $customer_id The customer_id property
    */
    private ?int $customer_id = null;
    
    /**
     * @var int|null $expiring_soon The expiring_soon property
    */
    private ?int $expiring_soon = null;
    
    /**
     * @var int|null $id The id property
    */
    private ?int $id = null;
    
    /**
     * @var string|null $updated_at The updated_at property
    */
    private ?string $updated_at = null;
    
    /**
     * Instantiates a new WalletData and sets the default values.
    */
    public function __construct() {
        $this->setAdditionalData([]);
    }

    /**
     * Creates a new instance of the appropriate class based on discriminator value
     * @param ParseNode $parseNode The parse node to use to read the discriminator value and create the object
     * @return WalletData
    */
    public static function createFromDiscriminatorValue(ParseNode $parseNode): WalletData {
        return new WalletData();
    }

    /**
     * Gets the AdditionalData property value. Stores additional data not described in the OpenAPI description found when deserializing. Can be used for serialization as well.
     * @return array<string, mixed>|null
    */
    public function getAdditionalData(): ?array {
        return $this->additionalData;
    }

    /**
     * Gets the balance property value. The balance property
     * @return int|null
    */
    public function getBalance(): ?int {
        return $this->balance;
    }

    /**
     * Gets the created_at property value. The created_at property
     * @return string|null
    */
    public function getCreatedAt(): ?string {
        return $this->created_at;
    }

    /**
     * Gets the customer_id property value. The customer_id property
     * @return int|null
    */
    public function getCustomerId(): ?int {
        return $this->customer_id;
    }

    /**
     * Gets the expiring_soon property value. The expiring_soon property
     * @return int|null
    */
    public function getExpiringSoon(): ?int {
        return $this->expiring_soon;
    }

    /**
     * The deserialization information for the current model
     * @return array<string, callable(ParseNode): void>
    */
    public function getFieldDeserializers(): array {
        $o = $this;
        return  [
            'balance' => fn(ParseNode $n) => $o->setBalance($n->getIntegerValue()),
            'created_at' => fn(ParseNode $n) => $o->setCreatedAt($n->getStringValue()),
            'customer_id' => fn(ParseNode $n) => $o->setCustomerId($n->getIntegerValue()),
            'expiring_soon' => fn(ParseNode $n) => $o->setExpiringSoon($n->getIntegerValue()),
            'id' => fn(ParseNode $n) => $o->setId($n->getIntegerValue()),
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
        $writer->writeIntegerValue('balance', $this->getBalance());
        $writer->writeStringValue('created_at', $this->getCreatedAt());
        $writer->writeIntegerValue('customer_id', $this->getCustomerId());
        $writer->writeIntegerValue('expiring_soon', $this->getExpiringSoon());
        $writer->writeIntegerValue('id', $this->getId());
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
     * Sets the balance property value. The balance property
     * @param int|null $value Value to set for the balance property.
    */
    public function setBalance(?int $value): void {
        $this->balance = $value;
    }

    /**
     * Sets the created_at property value. The created_at property
     * @param string|null $value Value to set for the created_at property.
    */
    public function setCreatedAt(?string $value): void {
        $this->created_at = $value;
    }

    /**
     * Sets the customer_id property value. The customer_id property
     * @param int|null $value Value to set for the customer_id property.
    */
    public function setCustomerId(?int $value): void {
        $this->customer_id = $value;
    }

    /**
     * Sets the expiring_soon property value. The expiring_soon property
     * @param int|null $value Value to set for the expiring_soon property.
    */
    public function setExpiringSoon(?int $value): void {
        $this->expiring_soon = $value;
    }

    /**
     * Sets the id property value. The id property
     * @param int|null $value Value to set for the id property.
    */
    public function setId(?int $value): void {
        $this->id = $value;
    }

    /**
     * Sets the updated_at property value. The updated_at property
     * @param string|null $value Value to set for the updated_at property.
    */
    public function setUpdatedAt(?string $value): void {
        $this->updated_at = $value;
    }

}
