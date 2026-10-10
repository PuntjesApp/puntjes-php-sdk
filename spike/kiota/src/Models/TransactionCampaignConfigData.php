<?php

namespace Puntjes\Spike\Kiota\Models;

use Microsoft\Kiota\Abstractions\Serialization\AdditionalDataHolder;
use Microsoft\Kiota\Abstractions\Serialization\Parsable;
use Microsoft\Kiota\Abstractions\Serialization\ParseNode;
use Microsoft\Kiota\Abstractions\Serialization\SerializationWriter;
use Microsoft\Kiota\Abstractions\Types\TypeUtils;

class TransactionCampaignConfigData implements AdditionalDataHolder, Parsable 
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
     * @var int|null $max_points_per_purchase The most this campaign pays out on one purchase.
    */
    private ?int $max_points_per_purchase = null;
    
    /**
     * @var int|null $points The points added, when `reward_type` is `fixed_points`.
    */
    private ?int $points = null;
    
    /**
     * @var array<CampaignProductRuleData>|null $products The products the campaign is limited to, when `scope` is `products`.
    */
    private ?array $products = null;
    
    /**
     * @var TransactionCampaignConfigData_reward_type|null $reward_type Multiply the points earned, or add a fixed number. Absent reads as `multiplier`.
    */
    private ?TransactionCampaignConfigData_reward_type $reward_type = null;
    
    /**
     * @var TransactionCampaignConfigData_scope|null $scope `whole` and `whole_purchase` both mean the whole purchase. Absent reads as `whole`.
    */
    private ?TransactionCampaignConfigData_scope $scope = null;
    
    /**
     * @var bool|null $stackable Whether it pays out beside another campaign. Older rows carry `combineerbaar`.
    */
    private ?bool $stackable = null;
    
    /**
     * @var TransactionCampaignConfigData_type|null $type Which shape this is.
    */
    private ?TransactionCampaignConfigData_type $type = null;
    
    /**
     * Instantiates a new TransactionCampaignConfigData and sets the default values.
    */
    public function __construct() {
        $this->setAdditionalData([]);
    }

    /**
     * Creates a new instance of the appropriate class based on discriminator value
     * @param ParseNode $parseNode The parse node to use to read the discriminator value and create the object
     * @return TransactionCampaignConfigData
    */
    public static function createFromDiscriminatorValue(ParseNode $parseNode): TransactionCampaignConfigData {
        return new TransactionCampaignConfigData();
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
            'max_points_per_purchase' => fn(ParseNode $n) => $o->setMaxPointsPerPurchase($n->getIntegerValue()),
            'points' => fn(ParseNode $n) => $o->setPoints($n->getIntegerValue()),
            'products' => fn(ParseNode $n) => $o->setProducts($n->getCollectionOfObjectValues([CampaignProductRuleData::class, 'createFromDiscriminatorValue'])),
            'reward_type' => fn(ParseNode $n) => $o->setRewardType($n->getEnumValue(TransactionCampaignConfigData_reward_type::class)),
            'scope' => fn(ParseNode $n) => $o->setScope($n->getEnumValue(TransactionCampaignConfigData_scope::class)),
            'stackable' => fn(ParseNode $n) => $o->setStackable($n->getBooleanValue()),
            'type' => fn(ParseNode $n) => $o->setType($n->getEnumValue(TransactionCampaignConfigData_type::class)),
        ];
    }

    /**
     * Gets the max_points_per_purchase property value. The most this campaign pays out on one purchase.
     * @return int|null
    */
    public function getMaxPointsPerPurchase(): ?int {
        return $this->max_points_per_purchase;
    }

    /**
     * Gets the points property value. The points added, when `reward_type` is `fixed_points`.
     * @return int|null
    */
    public function getPoints(): ?int {
        return $this->points;
    }

    /**
     * Gets the products property value. The products the campaign is limited to, when `scope` is `products`.
     * @return array<CampaignProductRuleData>|null
    */
    public function getProducts(): ?array {
        return $this->products;
    }

    /**
     * Gets the reward_type property value. Multiply the points earned, or add a fixed number. Absent reads as `multiplier`.
     * @return TransactionCampaignConfigData_reward_type|null
    */
    public function getRewardType(): ?TransactionCampaignConfigData_reward_type {
        return $this->reward_type;
    }

    /**
     * Gets the scope property value. `whole` and `whole_purchase` both mean the whole purchase. Absent reads as `whole`.
     * @return TransactionCampaignConfigData_scope|null
    */
    public function getScope(): ?TransactionCampaignConfigData_scope {
        return $this->scope;
    }

    /**
     * Gets the stackable property value. Whether it pays out beside another campaign. Older rows carry `combineerbaar`.
     * @return bool|null
    */
    public function getStackable(): ?bool {
        return $this->stackable;
    }

    /**
     * Gets the type property value. Which shape this is.
     * @return TransactionCampaignConfigData_type|null
    */
    public function getType(): ?TransactionCampaignConfigData_type {
        return $this->type;
    }

    /**
     * Serializes information the current object
     * @param SerializationWriter $writer Serialization writer to use to serialize this model
    */
    public function serialize(SerializationWriter $writer): void {
        $writer->writeCollectionOfPrimitiveValues('branch_ids', $this->getBranchIds());
        $writer->writeIntegerValue('max_points_per_purchase', $this->getMaxPointsPerPurchase());
        $writer->writeIntegerValue('points', $this->getPoints());
        $writer->writeCollectionOfObjectValues('products', $this->getProducts());
        $writer->writeEnumValue('reward_type', $this->getRewardType());
        $writer->writeEnumValue('scope', $this->getScope());
        $writer->writeBooleanValue('stackable', $this->getStackable());
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
     * Sets the max_points_per_purchase property value. The most this campaign pays out on one purchase.
     * @param int|null $value Value to set for the max_points_per_purchase property.
    */
    public function setMaxPointsPerPurchase(?int $value): void {
        $this->max_points_per_purchase = $value;
    }

    /**
     * Sets the points property value. The points added, when `reward_type` is `fixed_points`.
     * @param int|null $value Value to set for the points property.
    */
    public function setPoints(?int $value): void {
        $this->points = $value;
    }

    /**
     * Sets the products property value. The products the campaign is limited to, when `scope` is `products`.
     * @param array<CampaignProductRuleData>|null $value Value to set for the products property.
    */
    public function setProducts(?array $value): void {
        $this->products = $value;
    }

    /**
     * Sets the reward_type property value. Multiply the points earned, or add a fixed number. Absent reads as `multiplier`.
     * @param TransactionCampaignConfigData_reward_type|null $value Value to set for the reward_type property.
    */
    public function setRewardType(?TransactionCampaignConfigData_reward_type $value): void {
        $this->reward_type = $value;
    }

    /**
     * Sets the scope property value. `whole` and `whole_purchase` both mean the whole purchase. Absent reads as `whole`.
     * @param TransactionCampaignConfigData_scope|null $value Value to set for the scope property.
    */
    public function setScope(?TransactionCampaignConfigData_scope $value): void {
        $this->scope = $value;
    }

    /**
     * Sets the stackable property value. Whether it pays out beside another campaign. Older rows carry `combineerbaar`.
     * @param bool|null $value Value to set for the stackable property.
    */
    public function setStackable(?bool $value): void {
        $this->stackable = $value;
    }

    /**
     * Sets the type property value. Which shape this is.
     * @param TransactionCampaignConfigData_type|null $value Value to set for the type property.
    */
    public function setType(?TransactionCampaignConfigData_type $value): void {
        $this->type = $value;
    }

}
