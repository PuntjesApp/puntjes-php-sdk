<?php

namespace Puntjes\Spike\Kiota\Models;

use Microsoft\Kiota\Abstractions\Serialization\AdditionalDataHolder;
use Microsoft\Kiota\Abstractions\Serialization\Parsable;
use Microsoft\Kiota\Abstractions\Serialization\ParseNode;
use Microsoft\Kiota\Abstractions\Serialization\SerializationWriter;

class CampaignListData implements AdditionalDataHolder, Parsable 
{
    /**
     * @var array<string, mixed>|null $additionalData Stores additional data not described in the OpenAPI description found when deserializing. Can be used for serialization as well.
    */
    private ?array $additionalData = null;
    
    /**
     * @var array<BranchReferenceData>|null $branches null means every branch
    */
    private ?array $branches = null;
    
    /**
     * @var CampaignListData_config|null $config Its `type` says which shape this is.
    */
    private ?CampaignListData_config $config = null;
    
    /**
     * @var string|null $created_at The created_at property
    */
    private ?string $created_at = null;
    
    /**
     * @var string|null $ends_at The ends_at property
    */
    private ?string $ends_at = null;
    
    /**
     * @var string|null $family The family property
    */
    private ?string $family = null;
    
    /**
     * @var int|null $id The id property
    */
    private ?int $id = null;
    
    /**
     * @var float|null $min_transaction_amount The min_transaction_amount property
    */
    private ?float $min_transaction_amount = null;
    
    /**
     * @var string|null $moment The moment property
    */
    private ?string $moment = null;
    
    /**
     * @var int|null $multiplier The multiplier property
    */
    private ?int $multiplier = null;
    
    /**
     * @var string|null $name The name property
    */
    private ?string $name = null;
    
    /**
     * @var CampaignListData_recurrence_config|null $recurrence_config Its `type` says which shape this is.
    */
    private ?CampaignListData_recurrence_config $recurrence_config = null;
    
    /**
     * @var string|null $recurrence_type The recurrence_type property
    */
    private ?string $recurrence_type = null;
    
    /**
     * @var string|null $schedule_summary The schedule_summary property
    */
    private ?string $schedule_summary = null;
    
    /**
     * @var string|null $starts_at The starts_at property
    */
    private ?string $starts_at = null;
    
    /**
     * @var CampaignStatusData|null $status The status property
    */
    private ?CampaignStatusData $status = null;
    
    /**
     * @var string|null $updated_at The updated_at property
    */
    private ?string $updated_at = null;
    
    /**
     * @var int|null $version The version property
    */
    private ?int $version = null;
    
    /**
     * Instantiates a new CampaignListData and sets the default values.
    */
    public function __construct() {
        $this->setAdditionalData([]);
    }

    /**
     * Creates a new instance of the appropriate class based on discriminator value
     * @param ParseNode $parseNode The parse node to use to read the discriminator value and create the object
     * @return CampaignListData
    */
    public static function createFromDiscriminatorValue(ParseNode $parseNode): CampaignListData {
        return new CampaignListData();
    }

    /**
     * Gets the AdditionalData property value. Stores additional data not described in the OpenAPI description found when deserializing. Can be used for serialization as well.
     * @return array<string, mixed>|null
    */
    public function getAdditionalData(): ?array {
        return $this->additionalData;
    }

    /**
     * Gets the branches property value. null means every branch
     * @return array<BranchReferenceData>|null
    */
    public function getBranches(): ?array {
        return $this->branches;
    }

    /**
     * Gets the config property value. Its `type` says which shape this is.
     * @return CampaignListData_config|null
    */
    public function getConfig(): ?CampaignListData_config {
        return $this->config;
    }

    /**
     * Gets the created_at property value. The created_at property
     * @return string|null
    */
    public function getCreatedAt(): ?string {
        return $this->created_at;
    }

    /**
     * Gets the ends_at property value. The ends_at property
     * @return string|null
    */
    public function getEndsAt(): ?string {
        return $this->ends_at;
    }

    /**
     * Gets the family property value. The family property
     * @return string|null
    */
    public function getFamily(): ?string {
        return $this->family;
    }

    /**
     * The deserialization information for the current model
     * @return array<string, callable(ParseNode): void>
    */
    public function getFieldDeserializers(): array {
        $o = $this;
        return  [
            'branches' => fn(ParseNode $n) => $o->setBranches($n->getCollectionOfObjectValues([BranchReferenceData::class, 'createFromDiscriminatorValue'])),
            'config' => fn(ParseNode $n) => $o->setConfig($n->getObjectValue([CampaignListData_config::class, 'createFromDiscriminatorValue'])),
            'created_at' => fn(ParseNode $n) => $o->setCreatedAt($n->getStringValue()),
            'ends_at' => fn(ParseNode $n) => $o->setEndsAt($n->getStringValue()),
            'family' => fn(ParseNode $n) => $o->setFamily($n->getStringValue()),
            'id' => fn(ParseNode $n) => $o->setId($n->getIntegerValue()),
            'min_transaction_amount' => fn(ParseNode $n) => $o->setMinTransactionAmount($n->getFloatValue()),
            'moment' => fn(ParseNode $n) => $o->setMoment($n->getStringValue()),
            'multiplier' => fn(ParseNode $n) => $o->setMultiplier($n->getIntegerValue()),
            'name' => fn(ParseNode $n) => $o->setName($n->getStringValue()),
            'recurrence_config' => fn(ParseNode $n) => $o->setRecurrenceConfig($n->getObjectValue([CampaignListData_recurrence_config::class, 'createFromDiscriminatorValue'])),
            'recurrence_type' => fn(ParseNode $n) => $o->setRecurrenceType($n->getStringValue()),
            'schedule_summary' => fn(ParseNode $n) => $o->setScheduleSummary($n->getStringValue()),
            'starts_at' => fn(ParseNode $n) => $o->setStartsAt($n->getStringValue()),
            'status' => fn(ParseNode $n) => $o->setStatus($n->getObjectValue([CampaignStatusData::class, 'createFromDiscriminatorValue'])),
            'updated_at' => fn(ParseNode $n) => $o->setUpdatedAt($n->getStringValue()),
            'version' => fn(ParseNode $n) => $o->setVersion($n->getIntegerValue()),
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
     * Gets the min_transaction_amount property value. The min_transaction_amount property
     * @return float|null
    */
    public function getMinTransactionAmount(): ?float {
        return $this->min_transaction_amount;
    }

    /**
     * Gets the moment property value. The moment property
     * @return string|null
    */
    public function getMoment(): ?string {
        return $this->moment;
    }

    /**
     * Gets the multiplier property value. The multiplier property
     * @return int|null
    */
    public function getMultiplier(): ?int {
        return $this->multiplier;
    }

    /**
     * Gets the name property value. The name property
     * @return string|null
    */
    public function getName(): ?string {
        return $this->name;
    }

    /**
     * Gets the recurrence_config property value. Its `type` says which shape this is.
     * @return CampaignListData_recurrence_config|null
    */
    public function getRecurrenceConfig(): ?CampaignListData_recurrence_config {
        return $this->recurrence_config;
    }

    /**
     * Gets the recurrence_type property value. The recurrence_type property
     * @return string|null
    */
    public function getRecurrenceType(): ?string {
        return $this->recurrence_type;
    }

    /**
     * Gets the schedule_summary property value. The schedule_summary property
     * @return string|null
    */
    public function getScheduleSummary(): ?string {
        return $this->schedule_summary;
    }

    /**
     * Gets the starts_at property value. The starts_at property
     * @return string|null
    */
    public function getStartsAt(): ?string {
        return $this->starts_at;
    }

    /**
     * Gets the status property value. The status property
     * @return CampaignStatusData|null
    */
    public function getStatus(): ?CampaignStatusData {
        return $this->status;
    }

    /**
     * Gets the updated_at property value. The updated_at property
     * @return string|null
    */
    public function getUpdatedAt(): ?string {
        return $this->updated_at;
    }

    /**
     * Gets the version property value. The version property
     * @return int|null
    */
    public function getVersion(): ?int {
        return $this->version;
    }

    /**
     * Serializes information the current object
     * @param SerializationWriter $writer Serialization writer to use to serialize this model
    */
    public function serialize(SerializationWriter $writer): void {
        $writer->writeCollectionOfObjectValues('branches', $this->getBranches());
        $writer->writeObjectValue('config', $this->getConfig());
        $writer->writeStringValue('created_at', $this->getCreatedAt());
        $writer->writeStringValue('ends_at', $this->getEndsAt());
        $writer->writeStringValue('family', $this->getFamily());
        $writer->writeIntegerValue('id', $this->getId());
        $writer->writeFloatValue('min_transaction_amount', $this->getMinTransactionAmount());
        $writer->writeStringValue('moment', $this->getMoment());
        $writer->writeIntegerValue('multiplier', $this->getMultiplier());
        $writer->writeStringValue('name', $this->getName());
        $writer->writeObjectValue('recurrence_config', $this->getRecurrenceConfig());
        $writer->writeStringValue('recurrence_type', $this->getRecurrenceType());
        $writer->writeStringValue('schedule_summary', $this->getScheduleSummary());
        $writer->writeStringValue('starts_at', $this->getStartsAt());
        $writer->writeObjectValue('status', $this->getStatus());
        $writer->writeStringValue('updated_at', $this->getUpdatedAt());
        $writer->writeIntegerValue('version', $this->getVersion());
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
     * Sets the branches property value. null means every branch
     * @param array<BranchReferenceData>|null $value Value to set for the branches property.
    */
    public function setBranches(?array $value): void {
        $this->branches = $value;
    }

    /**
     * Sets the config property value. Its `type` says which shape this is.
     * @param CampaignListData_config|null $value Value to set for the config property.
    */
    public function setConfig(?CampaignListData_config $value): void {
        $this->config = $value;
    }

    /**
     * Sets the created_at property value. The created_at property
     * @param string|null $value Value to set for the created_at property.
    */
    public function setCreatedAt(?string $value): void {
        $this->created_at = $value;
    }

    /**
     * Sets the ends_at property value. The ends_at property
     * @param string|null $value Value to set for the ends_at property.
    */
    public function setEndsAt(?string $value): void {
        $this->ends_at = $value;
    }

    /**
     * Sets the family property value. The family property
     * @param string|null $value Value to set for the family property.
    */
    public function setFamily(?string $value): void {
        $this->family = $value;
    }

    /**
     * Sets the id property value. The id property
     * @param int|null $value Value to set for the id property.
    */
    public function setId(?int $value): void {
        $this->id = $value;
    }

    /**
     * Sets the min_transaction_amount property value. The min_transaction_amount property
     * @param float|null $value Value to set for the min_transaction_amount property.
    */
    public function setMinTransactionAmount(?float $value): void {
        $this->min_transaction_amount = $value;
    }

    /**
     * Sets the moment property value. The moment property
     * @param string|null $value Value to set for the moment property.
    */
    public function setMoment(?string $value): void {
        $this->moment = $value;
    }

    /**
     * Sets the multiplier property value. The multiplier property
     * @param int|null $value Value to set for the multiplier property.
    */
    public function setMultiplier(?int $value): void {
        $this->multiplier = $value;
    }

    /**
     * Sets the name property value. The name property
     * @param string|null $value Value to set for the name property.
    */
    public function setName(?string $value): void {
        $this->name = $value;
    }

    /**
     * Sets the recurrence_config property value. Its `type` says which shape this is.
     * @param CampaignListData_recurrence_config|null $value Value to set for the recurrence_config property.
    */
    public function setRecurrenceConfig(?CampaignListData_recurrence_config $value): void {
        $this->recurrence_config = $value;
    }

    /**
     * Sets the recurrence_type property value. The recurrence_type property
     * @param string|null $value Value to set for the recurrence_type property.
    */
    public function setRecurrenceType(?string $value): void {
        $this->recurrence_type = $value;
    }

    /**
     * Sets the schedule_summary property value. The schedule_summary property
     * @param string|null $value Value to set for the schedule_summary property.
    */
    public function setScheduleSummary(?string $value): void {
        $this->schedule_summary = $value;
    }

    /**
     * Sets the starts_at property value. The starts_at property
     * @param string|null $value Value to set for the starts_at property.
    */
    public function setStartsAt(?string $value): void {
        $this->starts_at = $value;
    }

    /**
     * Sets the status property value. The status property
     * @param CampaignStatusData|null $value Value to set for the status property.
    */
    public function setStatus(?CampaignStatusData $value): void {
        $this->status = $value;
    }

    /**
     * Sets the updated_at property value. The updated_at property
     * @param string|null $value Value to set for the updated_at property.
    */
    public function setUpdatedAt(?string $value): void {
        $this->updated_at = $value;
    }

    /**
     * Sets the version property value. The version property
     * @param int|null $value Value to set for the version property.
    */
    public function setVersion(?int $value): void {
        $this->version = $value;
    }

}
