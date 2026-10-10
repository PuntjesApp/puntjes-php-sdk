<?php

namespace Puntjes\Spike\Kiota\Models;

use Microsoft\Kiota\Abstractions\Serialization\AdditionalDataHolder;
use Microsoft\Kiota\Abstractions\Serialization\Parsable;
use Microsoft\Kiota\Abstractions\Serialization\ParseNode;
use Microsoft\Kiota\Abstractions\Serialization\SerializationWriter;
use Microsoft\Kiota\Abstractions\Types\TypeUtils;

class AnniversaryCampaignConfigData implements AdditionalDataHolder, Parsable 
{
    /**
     * @var array<string, mixed>|null $additionalData Stores additional data not described in the OpenAPI description found when deserializing. Can be used for serialization as well.
    */
    private ?array $additionalData = null;
    
    /**
     * @var bool|null $backfill Whether customers who passed the mark before the campaign started also get it.
    */
    private ?bool $backfill = null;
    
    /**
     * @var array<int>|null $branch_ids The branches this campaign runs in. Absent or empty means all of them.
    */
    private ?array $branch_ids = null;
    
    /**
     * @var AnniversaryCampaignConfigData_gift|null $gift What the customer gets. Its `type` says which shape this is.
    */
    private ?AnniversaryCampaignConfigData_gift $gift = null;
    
    /**
     * @var AnniversaryCampaignConfigData_type|null $type Which shape this is.
    */
    private ?AnniversaryCampaignConfigData_type $type = null;
    
    /**
     * @var int|null $years How many years after joining the gift is sent.
    */
    private ?int $years = null;
    
    /**
     * Instantiates a new AnniversaryCampaignConfigData and sets the default values.
    */
    public function __construct() {
        $this->setAdditionalData([]);
    }

    /**
     * Creates a new instance of the appropriate class based on discriminator value
     * @param ParseNode $parseNode The parse node to use to read the discriminator value and create the object
     * @return AnniversaryCampaignConfigData
    */
    public static function createFromDiscriminatorValue(ParseNode $parseNode): AnniversaryCampaignConfigData {
        return new AnniversaryCampaignConfigData();
    }

    /**
     * Gets the AdditionalData property value. Stores additional data not described in the OpenAPI description found when deserializing. Can be used for serialization as well.
     * @return array<string, mixed>|null
    */
    public function getAdditionalData(): ?array {
        return $this->additionalData;
    }

    /**
     * Gets the backfill property value. Whether customers who passed the mark before the campaign started also get it.
     * @return bool|null
    */
    public function getBackfill(): ?bool {
        return $this->backfill;
    }

    /**
     * Gets the branch_ids property value. The branches this campaign runs in. Absent or empty means all of them.
     * @return array<int>|null
    */
    public function getBranchIds(): ?array {
        return $this->branch_ids;
    }

    /**
     * The deserialization information for the current model
     * @return array<string, callable(ParseNode): void>
    */
    public function getFieldDeserializers(): array {
        $o = $this;
        return  [
            'backfill' => fn(ParseNode $n) => $o->setBackfill($n->getBooleanValue()),
            'branch_ids' => function (ParseNode $n) {
                $val = $n->getCollectionOfPrimitiveValues();
                if (is_array($val)) {
                    TypeUtils::validateCollectionValues($val, 'int');
                }
                /** @var array<int>|null $val */
                $this->setBranchIds($val);
            },
            'gift' => fn(ParseNode $n) => $o->setGift($n->getObjectValue([AnniversaryCampaignConfigData_gift::class, 'createFromDiscriminatorValue'])),
            'type' => fn(ParseNode $n) => $o->setType($n->getEnumValue(AnniversaryCampaignConfigData_type::class)),
            'years' => fn(ParseNode $n) => $o->setYears($n->getIntegerValue()),
        ];
    }

    /**
     * Gets the gift property value. What the customer gets. Its `type` says which shape this is.
     * @return AnniversaryCampaignConfigData_gift|null
    */
    public function getGift(): ?AnniversaryCampaignConfigData_gift {
        return $this->gift;
    }

    /**
     * Gets the type property value. Which shape this is.
     * @return AnniversaryCampaignConfigData_type|null
    */
    public function getType(): ?AnniversaryCampaignConfigData_type {
        return $this->type;
    }

    /**
     * Gets the years property value. How many years after joining the gift is sent.
     * @return int|null
    */
    public function getYears(): ?int {
        return $this->years;
    }

    /**
     * Serializes information the current object
     * @param SerializationWriter $writer Serialization writer to use to serialize this model
    */
    public function serialize(SerializationWriter $writer): void {
        $writer->writeBooleanValue('backfill', $this->getBackfill());
        $writer->writeCollectionOfPrimitiveValues('branch_ids', $this->getBranchIds());
        $writer->writeObjectValue('gift', $this->getGift());
        $writer->writeEnumValue('type', $this->getType());
        $writer->writeIntegerValue('years', $this->getYears());
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
     * Sets the backfill property value. Whether customers who passed the mark before the campaign started also get it.
     * @param bool|null $value Value to set for the backfill property.
    */
    public function setBackfill(?bool $value): void {
        $this->backfill = $value;
    }

    /**
     * Sets the branch_ids property value. The branches this campaign runs in. Absent or empty means all of them.
     * @param array<int>|null $value Value to set for the branch_ids property.
    */
    public function setBranchIds(?array $value): void {
        $this->branch_ids = $value;
    }

    /**
     * Sets the gift property value. What the customer gets. Its `type` says which shape this is.
     * @param AnniversaryCampaignConfigData_gift|null $value Value to set for the gift property.
    */
    public function setGift(?AnniversaryCampaignConfigData_gift $value): void {
        $this->gift = $value;
    }

    /**
     * Sets the type property value. Which shape this is.
     * @param AnniversaryCampaignConfigData_type|null $value Value to set for the type property.
    */
    public function setType(?AnniversaryCampaignConfigData_type $value): void {
        $this->type = $value;
    }

    /**
     * Sets the years property value. How many years after joining the gift is sent.
     * @param int|null $value Value to set for the years property.
    */
    public function setYears(?int $value): void {
        $this->years = $value;
    }

}
