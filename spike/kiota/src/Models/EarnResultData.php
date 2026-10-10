<?php

namespace Puntjes\Spike\Kiota\Models;

use Microsoft\Kiota\Abstractions\Serialization\AdditionalDataHolder;
use Microsoft\Kiota\Abstractions\Serialization\Parsable;
use Microsoft\Kiota\Abstractions\Serialization\ParseNode;
use Microsoft\Kiota\Abstractions\Serialization\SerializationWriter;

class EarnResultData implements AdditionalDataHolder, Parsable 
{
    /**
     * @var array<string, mixed>|null $additionalData Stores additional data not described in the OpenAPI description found when deserializing. Can be used for serialization as well.
    */
    private ?array $additionalData = null;
    
    /**
     * @var int|null $campaign_version The campaign_version property
    */
    private ?int $campaign_version = null;
    
    /**
     * @var string|null $family The family property
    */
    private ?string $family = null;
    
    /**
     * @var array<EarnResultData_line_breakdown>|null $line_breakdown The line_breakdown property
    */
    private ?array $line_breakdown = null;
    
    /**
     * @var string|null $moment The moment property
    */
    private ?string $moment = null;
    
    /**
     * @var int|null $points_earned The points_earned property
    */
    private ?int $points_earned = null;
    
    /**
     * @var string|null $reason The reason property
    */
    private ?string $reason = null;
    
    /**
     * @var int|null $rule_id The rule_id property
    */
    private ?int $rule_id = null;
    
    /**
     * @var string|null $rule_name The rule_name property
    */
    private ?string $rule_name = null;
    
    /**
     * @var string|null $rule_type The rule_type property
    */
    private ?string $rule_type = null;
    
    /**
     * @var int|null $suppressed_by The suppressed_by property
    */
    private ?int $suppressed_by = null;
    
    /**
     * Instantiates a new EarnResultData and sets the default values.
    */
    public function __construct() {
        $this->setAdditionalData([]);
    }

    /**
     * Creates a new instance of the appropriate class based on discriminator value
     * @param ParseNode $parseNode The parse node to use to read the discriminator value and create the object
     * @return EarnResultData
    */
    public static function createFromDiscriminatorValue(ParseNode $parseNode): EarnResultData {
        return new EarnResultData();
    }

    /**
     * Gets the AdditionalData property value. Stores additional data not described in the OpenAPI description found when deserializing. Can be used for serialization as well.
     * @return array<string, mixed>|null
    */
    public function getAdditionalData(): ?array {
        return $this->additionalData;
    }

    /**
     * Gets the campaign_version property value. The campaign_version property
     * @return int|null
    */
    public function getCampaignVersion(): ?int {
        return $this->campaign_version;
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
            'campaign_version' => fn(ParseNode $n) => $o->setCampaignVersion($n->getIntegerValue()),
            'family' => fn(ParseNode $n) => $o->setFamily($n->getStringValue()),
            'line_breakdown' => fn(ParseNode $n) => $o->setLineBreakdown($n->getCollectionOfObjectValues([EarnResultData_line_breakdown::class, 'createFromDiscriminatorValue'])),
            'moment' => fn(ParseNode $n) => $o->setMoment($n->getStringValue()),
            'points_earned' => fn(ParseNode $n) => $o->setPointsEarned($n->getIntegerValue()),
            'reason' => fn(ParseNode $n) => $o->setReason($n->getStringValue()),
            'rule_id' => fn(ParseNode $n) => $o->setRuleId($n->getIntegerValue()),
            'rule_name' => fn(ParseNode $n) => $o->setRuleName($n->getStringValue()),
            'rule_type' => fn(ParseNode $n) => $o->setRuleType($n->getStringValue()),
            'suppressed_by' => fn(ParseNode $n) => $o->setSuppressedBy($n->getIntegerValue()),
        ];
    }

    /**
     * Gets the line_breakdown property value. The line_breakdown property
     * @return array<EarnResultData_line_breakdown>|null
    */
    public function getLineBreakdown(): ?array {
        return $this->line_breakdown;
    }

    /**
     * Gets the moment property value. The moment property
     * @return string|null
    */
    public function getMoment(): ?string {
        return $this->moment;
    }

    /**
     * Gets the points_earned property value. The points_earned property
     * @return int|null
    */
    public function getPointsEarned(): ?int {
        return $this->points_earned;
    }

    /**
     * Gets the reason property value. The reason property
     * @return string|null
    */
    public function getReason(): ?string {
        return $this->reason;
    }

    /**
     * Gets the rule_id property value. The rule_id property
     * @return int|null
    */
    public function getRuleId(): ?int {
        return $this->rule_id;
    }

    /**
     * Gets the rule_name property value. The rule_name property
     * @return string|null
    */
    public function getRuleName(): ?string {
        return $this->rule_name;
    }

    /**
     * Gets the rule_type property value. The rule_type property
     * @return string|null
    */
    public function getRuleType(): ?string {
        return $this->rule_type;
    }

    /**
     * Gets the suppressed_by property value. The suppressed_by property
     * @return int|null
    */
    public function getSuppressedBy(): ?int {
        return $this->suppressed_by;
    }

    /**
     * Serializes information the current object
     * @param SerializationWriter $writer Serialization writer to use to serialize this model
    */
    public function serialize(SerializationWriter $writer): void {
        $writer->writeIntegerValue('campaign_version', $this->getCampaignVersion());
        $writer->writeStringValue('family', $this->getFamily());
        $writer->writeCollectionOfObjectValues('line_breakdown', $this->getLineBreakdown());
        $writer->writeStringValue('moment', $this->getMoment());
        $writer->writeIntegerValue('points_earned', $this->getPointsEarned());
        $writer->writeStringValue('reason', $this->getReason());
        $writer->writeIntegerValue('rule_id', $this->getRuleId());
        $writer->writeStringValue('rule_name', $this->getRuleName());
        $writer->writeStringValue('rule_type', $this->getRuleType());
        $writer->writeIntegerValue('suppressed_by', $this->getSuppressedBy());
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
     * Sets the campaign_version property value. The campaign_version property
     * @param int|null $value Value to set for the campaign_version property.
    */
    public function setCampaignVersion(?int $value): void {
        $this->campaign_version = $value;
    }

    /**
     * Sets the family property value. The family property
     * @param string|null $value Value to set for the family property.
    */
    public function setFamily(?string $value): void {
        $this->family = $value;
    }

    /**
     * Sets the line_breakdown property value. The line_breakdown property
     * @param array<EarnResultData_line_breakdown>|null $value Value to set for the line_breakdown property.
    */
    public function setLineBreakdown(?array $value): void {
        $this->line_breakdown = $value;
    }

    /**
     * Sets the moment property value. The moment property
     * @param string|null $value Value to set for the moment property.
    */
    public function setMoment(?string $value): void {
        $this->moment = $value;
    }

    /**
     * Sets the points_earned property value. The points_earned property
     * @param int|null $value Value to set for the points_earned property.
    */
    public function setPointsEarned(?int $value): void {
        $this->points_earned = $value;
    }

    /**
     * Sets the reason property value. The reason property
     * @param string|null $value Value to set for the reason property.
    */
    public function setReason(?string $value): void {
        $this->reason = $value;
    }

    /**
     * Sets the rule_id property value. The rule_id property
     * @param int|null $value Value to set for the rule_id property.
    */
    public function setRuleId(?int $value): void {
        $this->rule_id = $value;
    }

    /**
     * Sets the rule_name property value. The rule_name property
     * @param string|null $value Value to set for the rule_name property.
    */
    public function setRuleName(?string $value): void {
        $this->rule_name = $value;
    }

    /**
     * Sets the rule_type property value. The rule_type property
     * @param string|null $value Value to set for the rule_type property.
    */
    public function setRuleType(?string $value): void {
        $this->rule_type = $value;
    }

    /**
     * Sets the suppressed_by property value. The suppressed_by property
     * @param int|null $value Value to set for the suppressed_by property.
    */
    public function setSuppressedBy(?int $value): void {
        $this->suppressed_by = $value;
    }

}
