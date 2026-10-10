<?php

namespace Puntjes\Spike\Kiota\Models;

use Microsoft\Kiota\Abstractions\Serialization\AdditionalDataHolder;
use Microsoft\Kiota\Abstractions\Serialization\Parsable;
use Microsoft\Kiota\Abstractions\Serialization\ParseNode;
use Microsoft\Kiota\Abstractions\Serialization\SerializationWriter;
use Microsoft\Kiota\Abstractions\Types\TypeUtils;

class NthPurchaseCampaignConfigData implements AdditionalDataHolder, Parsable 
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
     * @var int|null $every_n Which purchase triggers the gift. 3 means every third one.
    */
    private ?int $every_n = null;
    
    /**
     * @var NthPurchaseCampaignConfigData_gift|null $gift What the customer gets. Its `type` says which shape this is.
    */
    private ?NthPurchaseCampaignConfigData_gift $gift = null;
    
    /**
     * @var int|null $min_amount The smallest purchase that counts, in cents.
    */
    private ?int $min_amount = null;
    
    /**
     * @var NthPurchaseCampaignConfigData_type|null $type Which shape this is.
    */
    private ?NthPurchaseCampaignConfigData_type $type = null;
    
    /**
     * Instantiates a new NthPurchaseCampaignConfigData and sets the default values.
    */
    public function __construct() {
        $this->setAdditionalData([]);
    }

    /**
     * Creates a new instance of the appropriate class based on discriminator value
     * @param ParseNode $parseNode The parse node to use to read the discriminator value and create the object
     * @return NthPurchaseCampaignConfigData
    */
    public static function createFromDiscriminatorValue(ParseNode $parseNode): NthPurchaseCampaignConfigData {
        return new NthPurchaseCampaignConfigData();
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
     * Gets the every_n property value. Which purchase triggers the gift. 3 means every third one.
     * @return int|null
    */
    public function getEveryN(): ?int {
        return $this->every_n;
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
            'every_n' => fn(ParseNode $n) => $o->setEveryN($n->getIntegerValue()),
            'gift' => fn(ParseNode $n) => $o->setGift($n->getObjectValue([NthPurchaseCampaignConfigData_gift::class, 'createFromDiscriminatorValue'])),
            'min_amount' => fn(ParseNode $n) => $o->setMinAmount($n->getIntegerValue()),
            'type' => fn(ParseNode $n) => $o->setType($n->getEnumValue(NthPurchaseCampaignConfigData_type::class)),
        ];
    }

    /**
     * Gets the gift property value. What the customer gets. Its `type` says which shape this is.
     * @return NthPurchaseCampaignConfigData_gift|null
    */
    public function getGift(): ?NthPurchaseCampaignConfigData_gift {
        return $this->gift;
    }

    /**
     * Gets the min_amount property value. The smallest purchase that counts, in cents.
     * @return int|null
    */
    public function getMinAmount(): ?int {
        return $this->min_amount;
    }

    /**
     * Gets the type property value. Which shape this is.
     * @return NthPurchaseCampaignConfigData_type|null
    */
    public function getType(): ?NthPurchaseCampaignConfigData_type {
        return $this->type;
    }

    /**
     * Serializes information the current object
     * @param SerializationWriter $writer Serialization writer to use to serialize this model
    */
    public function serialize(SerializationWriter $writer): void {
        $writer->writeCollectionOfPrimitiveValues('branch_ids', $this->getBranchIds());
        $writer->writeIntegerValue('every_n', $this->getEveryN());
        $writer->writeObjectValue('gift', $this->getGift());
        $writer->writeIntegerValue('min_amount', $this->getMinAmount());
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
     * Sets the every_n property value. Which purchase triggers the gift. 3 means every third one.
     * @param int|null $value Value to set for the every_n property.
    */
    public function setEveryN(?int $value): void {
        $this->every_n = $value;
    }

    /**
     * Sets the gift property value. What the customer gets. Its `type` says which shape this is.
     * @param NthPurchaseCampaignConfigData_gift|null $value Value to set for the gift property.
    */
    public function setGift(?NthPurchaseCampaignConfigData_gift $value): void {
        $this->gift = $value;
    }

    /**
     * Sets the min_amount property value. The smallest purchase that counts, in cents.
     * @param int|null $value Value to set for the min_amount property.
    */
    public function setMinAmount(?int $value): void {
        $this->min_amount = $value;
    }

    /**
     * Sets the type property value. Which shape this is.
     * @param NthPurchaseCampaignConfigData_type|null $value Value to set for the type property.
    */
    public function setType(?NthPurchaseCampaignConfigData_type $value): void {
        $this->type = $value;
    }

}
