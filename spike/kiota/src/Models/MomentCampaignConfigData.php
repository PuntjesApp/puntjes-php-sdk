<?php

namespace Puntjes\Spike\Kiota\Models;

use Microsoft\Kiota\Abstractions\Serialization\AdditionalDataHolder;
use Microsoft\Kiota\Abstractions\Serialization\Parsable;
use Microsoft\Kiota\Abstractions\Serialization\ParseNode;
use Microsoft\Kiota\Abstractions\Serialization\SerializationWriter;
use Microsoft\Kiota\Abstractions\Types\TypeUtils;

class MomentCampaignConfigData implements AdditionalDataHolder, Parsable 
{
    /**
     * @var array<string, mixed>|null $additionalData Stores additional data not described in the OpenAPI description found when deserializing. Can be used for serialization as well.
    */
    private ?array $additionalData = null;
    
    /**
     * @var array<int>|null $branch_ids The branches this campaign runs in. Absent or empty means all of them.
    */
    private ?array $branch_ids = null;
    
    /**
     * @var MomentCampaignConfigData_gift|null $gift What the customer gets. Its `type` says which shape this is.
    */
    private ?MomentCampaignConfigData_gift $gift = null;
    
    /**
     * @var MomentCampaignConfigData_type|null $type Which shape this is.
    */
    private ?MomentCampaignConfigData_type $type = null;
    
    /**
     * Instantiates a new MomentCampaignConfigData and sets the default values.
    */
    public function __construct() {
        $this->setAdditionalData([]);
    }

    /**
     * Creates a new instance of the appropriate class based on discriminator value
     * @param ParseNode $parseNode The parse node to use to read the discriminator value and create the object
     * @return MomentCampaignConfigData
    */
    public static function createFromDiscriminatorValue(ParseNode $parseNode): MomentCampaignConfigData {
        return new MomentCampaignConfigData();
    }

    /**
     * Gets the AdditionalData property value. Stores additional data not described in the OpenAPI description found when deserializing. Can be used for serialization as well.
     * @return array<string, mixed>|null
    */
    public function getAdditionalData(): ?array {
        return $this->additionalData;
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
            'branch_ids' => function (ParseNode $n) {
                $val = $n->getCollectionOfPrimitiveValues();
                if (is_array($val)) {
                    TypeUtils::validateCollectionValues($val, 'int');
                }
                /** @var array<int>|null $val */
                $this->setBranchIds($val);
            },
            'gift' => fn(ParseNode $n) => $o->setGift($n->getObjectValue([MomentCampaignConfigData_gift::class, 'createFromDiscriminatorValue'])),
            'type' => fn(ParseNode $n) => $o->setType($n->getEnumValue(MomentCampaignConfigData_type::class)),
        ];
    }

    /**
     * Gets the gift property value. What the customer gets. Its `type` says which shape this is.
     * @return MomentCampaignConfigData_gift|null
    */
    public function getGift(): ?MomentCampaignConfigData_gift {
        return $this->gift;
    }

    /**
     * Gets the type property value. Which shape this is.
     * @return MomentCampaignConfigData_type|null
    */
    public function getType(): ?MomentCampaignConfigData_type {
        return $this->type;
    }

    /**
     * Serializes information the current object
     * @param SerializationWriter $writer Serialization writer to use to serialize this model
    */
    public function serialize(SerializationWriter $writer): void {
        $writer->writeCollectionOfPrimitiveValues('branch_ids', $this->getBranchIds());
        $writer->writeObjectValue('gift', $this->getGift());
        $writer->writeEnumValue('type', $this->getType());
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
     * Sets the branch_ids property value. The branches this campaign runs in. Absent or empty means all of them.
     * @param array<int>|null $value Value to set for the branch_ids property.
    */
    public function setBranchIds(?array $value): void {
        $this->branch_ids = $value;
    }

    /**
     * Sets the gift property value. What the customer gets. Its `type` says which shape this is.
     * @param MomentCampaignConfigData_gift|null $value Value to set for the gift property.
    */
    public function setGift(?MomentCampaignConfigData_gift $value): void {
        $this->gift = $value;
    }

    /**
     * Sets the type property value. Which shape this is.
     * @param MomentCampaignConfigData_type|null $value Value to set for the type property.
    */
    public function setType(?MomentCampaignConfigData_type $value): void {
        $this->type = $value;
    }

}
