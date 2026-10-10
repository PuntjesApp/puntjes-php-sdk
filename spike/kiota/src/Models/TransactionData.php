<?php

namespace Puntjes\Spike\Kiota\Models;

use Microsoft\Kiota\Abstractions\Serialization\AdditionalDataHolder;
use Microsoft\Kiota\Abstractions\Serialization\Parsable;
use Microsoft\Kiota\Abstractions\Serialization\ParseNode;
use Microsoft\Kiota\Abstractions\Serialization\SerializationWriter;

class TransactionData implements AdditionalDataHolder, Parsable 
{
    /**
     * @var array<string, mixed>|null $additionalData Stores additional data not described in the OpenAPI description found when deserializing. Can be used for serialization as well.
    */
    private ?array $additionalData = null;
    
    /**
     * @var BranchReferenceData|null $branch The branch property
    */
    private ?BranchReferenceData $branch = null;
    
    /**
     * @var string|null $created_at The created_at property
    */
    private ?string $created_at = null;
    
    /**
     * @var int|null $customer_id The customer_id property
    */
    private ?int $customer_id = null;
    
    /**
     * @var string|null $description The description property
    */
    private ?string $description = null;
    
    /**
     * @var string|null $external_reference The external_reference property
    */
    private ?string $external_reference = null;
    
    /**
     * @var int|null $id The id property
    */
    private ?int $id = null;
    
    /**
     * @var string|null $idempotency_key The idempotency_key property
    */
    private ?string $idempotency_key = null;
    
    /**
     * @var array<TransactionItemData>|null $items The items property
    */
    private ?array $items = null;
    
    /**
     * @var int|null $points_earned The points_earned property
    */
    private ?int $points_earned = null;
    
    /**
     * @var array<EarnResultData>|null $rules_applied The rules_applied property
    */
    private ?array $rules_applied = null;
    
    /**
     * @var int|null $total_amount The total_amount property
    */
    private ?int $total_amount = null;
    
    /**
     * Instantiates a new TransactionData and sets the default values.
    */
    public function __construct() {
        $this->setAdditionalData([]);
    }

    /**
     * Creates a new instance of the appropriate class based on discriminator value
     * @param ParseNode $parseNode The parse node to use to read the discriminator value and create the object
     * @return TransactionData
    */
    public static function createFromDiscriminatorValue(ParseNode $parseNode): TransactionData {
        return new TransactionData();
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
     * @return BranchReferenceData|null
    */
    public function getBranch(): ?BranchReferenceData {
        return $this->branch;
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
     * Gets the description property value. The description property
     * @return string|null
    */
    public function getDescription(): ?string {
        return $this->description;
    }

    /**
     * Gets the external_reference property value. The external_reference property
     * @return string|null
    */
    public function getExternalReference(): ?string {
        return $this->external_reference;
    }

    /**
     * The deserialization information for the current model
     * @return array<string, callable(ParseNode): void>
    */
    public function getFieldDeserializers(): array {
        $o = $this;
        return  [
            'branch' => fn(ParseNode $n) => $o->setBranch($n->getObjectValue([BranchReferenceData::class, 'createFromDiscriminatorValue'])),
            'created_at' => fn(ParseNode $n) => $o->setCreatedAt($n->getStringValue()),
            'customer_id' => fn(ParseNode $n) => $o->setCustomerId($n->getIntegerValue()),
            'description' => fn(ParseNode $n) => $o->setDescription($n->getStringValue()),
            'external_reference' => fn(ParseNode $n) => $o->setExternalReference($n->getStringValue()),
            'id' => fn(ParseNode $n) => $o->setId($n->getIntegerValue()),
            'idempotency_key' => fn(ParseNode $n) => $o->setIdempotencyKey($n->getStringValue()),
            'items' => fn(ParseNode $n) => $o->setItems($n->getCollectionOfObjectValues([TransactionItemData::class, 'createFromDiscriminatorValue'])),
            'points_earned' => fn(ParseNode $n) => $o->setPointsEarned($n->getIntegerValue()),
            'rules_applied' => fn(ParseNode $n) => $o->setRulesApplied($n->getCollectionOfObjectValues([EarnResultData::class, 'createFromDiscriminatorValue'])),
            'total_amount' => fn(ParseNode $n) => $o->setTotalAmount($n->getIntegerValue()),
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
     * Gets the idempotency_key property value. The idempotency_key property
     * @return string|null
    */
    public function getIdempotencyKey(): ?string {
        return $this->idempotency_key;
    }

    /**
     * Gets the items property value. The items property
     * @return array<TransactionItemData>|null
    */
    public function getItems(): ?array {
        return $this->items;
    }

    /**
     * Gets the points_earned property value. The points_earned property
     * @return int|null
    */
    public function getPointsEarned(): ?int {
        return $this->points_earned;
    }

    /**
     * Gets the rules_applied property value. The rules_applied property
     * @return array<EarnResultData>|null
    */
    public function getRulesApplied(): ?array {
        return $this->rules_applied;
    }

    /**
     * Gets the total_amount property value. The total_amount property
     * @return int|null
    */
    public function getTotalAmount(): ?int {
        return $this->total_amount;
    }

    /**
     * Serializes information the current object
     * @param SerializationWriter $writer Serialization writer to use to serialize this model
    */
    public function serialize(SerializationWriter $writer): void {
        $writer->writeObjectValue('branch', $this->getBranch());
        $writer->writeStringValue('created_at', $this->getCreatedAt());
        $writer->writeIntegerValue('customer_id', $this->getCustomerId());
        $writer->writeStringValue('description', $this->getDescription());
        $writer->writeStringValue('external_reference', $this->getExternalReference());
        $writer->writeIntegerValue('id', $this->getId());
        $writer->writeStringValue('idempotency_key', $this->getIdempotencyKey());
        $writer->writeCollectionOfObjectValues('items', $this->getItems());
        $writer->writeIntegerValue('points_earned', $this->getPointsEarned());
        $writer->writeCollectionOfObjectValues('rules_applied', $this->getRulesApplied());
        $writer->writeIntegerValue('total_amount', $this->getTotalAmount());
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
     * @param BranchReferenceData|null $value Value to set for the branch property.
    */
    public function setBranch(?BranchReferenceData $value): void {
        $this->branch = $value;
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
     * Sets the description property value. The description property
     * @param string|null $value Value to set for the description property.
    */
    public function setDescription(?string $value): void {
        $this->description = $value;
    }

    /**
     * Sets the external_reference property value. The external_reference property
     * @param string|null $value Value to set for the external_reference property.
    */
    public function setExternalReference(?string $value): void {
        $this->external_reference = $value;
    }

    /**
     * Sets the id property value. The id property
     * @param int|null $value Value to set for the id property.
    */
    public function setId(?int $value): void {
        $this->id = $value;
    }

    /**
     * Sets the idempotency_key property value. The idempotency_key property
     * @param string|null $value Value to set for the idempotency_key property.
    */
    public function setIdempotencyKey(?string $value): void {
        $this->idempotency_key = $value;
    }

    /**
     * Sets the items property value. The items property
     * @param array<TransactionItemData>|null $value Value to set for the items property.
    */
    public function setItems(?array $value): void {
        $this->items = $value;
    }

    /**
     * Sets the points_earned property value. The points_earned property
     * @param int|null $value Value to set for the points_earned property.
    */
    public function setPointsEarned(?int $value): void {
        $this->points_earned = $value;
    }

    /**
     * Sets the rules_applied property value. The rules_applied property
     * @param array<EarnResultData>|null $value Value to set for the rules_applied property.
    */
    public function setRulesApplied(?array $value): void {
        $this->rules_applied = $value;
    }

    /**
     * Sets the total_amount property value. The total_amount property
     * @param int|null $value Value to set for the total_amount property.
    */
    public function setTotalAmount(?int $value): void {
        $this->total_amount = $value;
    }

}
