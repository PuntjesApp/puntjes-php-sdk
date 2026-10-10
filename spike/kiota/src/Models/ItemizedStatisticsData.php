<?php

namespace Puntjes\Spike\Kiota\Models;

use Microsoft\Kiota\Abstractions\Serialization\AdditionalDataHolder;
use Microsoft\Kiota\Abstractions\Serialization\Parsable;
use Microsoft\Kiota\Abstractions\Serialization\ParseNode;
use Microsoft\Kiota\Abstractions\Serialization\SerializationWriter;

class ItemizedStatisticsData implements AdditionalDataHolder, Parsable 
{
    /**
     * @var array<string, mixed>|null $additionalData Stores additional data not described in the OpenAPI description found when deserializing. Can be used for serialization as well.
    */
    private ?array $additionalData = null;
    
    /**
     * @var float|null $average_items_per_order The average_items_per_order property
    */
    private ?float $average_items_per_order = null;
    
    /**
     * @var array<CategoryRevenueData>|null $categories The categories property
    */
    private ?array $categories = null;
    
    /**
     * @var int|null $orders The orders property
    */
    private ?int $orders = null;
    
    /**
     * @var int|null $other_no_item_detail_cents The other_no_item_detail_cents property
    */
    private ?int $other_no_item_detail_cents = null;
    
    /**
     * @var int|null $revenue_cents The revenue_cents property
    */
    private ?int $revenue_cents = null;
    
    /**
     * @var array<TopProductData>|null $top_products The top_products property
    */
    private ?array $top_products = null;
    
    /**
     * Instantiates a new ItemizedStatisticsData and sets the default values.
    */
    public function __construct() {
        $this->setAdditionalData([]);
    }

    /**
     * Creates a new instance of the appropriate class based on discriminator value
     * @param ParseNode $parseNode The parse node to use to read the discriminator value and create the object
     * @return ItemizedStatisticsData
    */
    public static function createFromDiscriminatorValue(ParseNode $parseNode): ItemizedStatisticsData {
        return new ItemizedStatisticsData();
    }

    /**
     * Gets the AdditionalData property value. Stores additional data not described in the OpenAPI description found when deserializing. Can be used for serialization as well.
     * @return array<string, mixed>|null
    */
    public function getAdditionalData(): ?array {
        return $this->additionalData;
    }

    /**
     * Gets the average_items_per_order property value. The average_items_per_order property
     * @return float|null
    */
    public function getAverageItemsPerOrder(): ?float {
        return $this->average_items_per_order;
    }

    /**
     * Gets the categories property value. The categories property
     * @return array<CategoryRevenueData>|null
    */
    public function getCategories(): ?array {
        return $this->categories;
    }

    /**
     * The deserialization information for the current model
     * @return array<string, callable(ParseNode): void>
    */
    public function getFieldDeserializers(): array {
        $o = $this;
        return  [
            'average_items_per_order' => fn(ParseNode $n) => $o->setAverageItemsPerOrder($n->getFloatValue()),
            'categories' => fn(ParseNode $n) => $o->setCategories($n->getCollectionOfObjectValues([CategoryRevenueData::class, 'createFromDiscriminatorValue'])),
            'orders' => fn(ParseNode $n) => $o->setOrders($n->getIntegerValue()),
            'other_no_item_detail_cents' => fn(ParseNode $n) => $o->setOtherNoItemDetailCents($n->getIntegerValue()),
            'revenue_cents' => fn(ParseNode $n) => $o->setRevenueCents($n->getIntegerValue()),
            'top_products' => fn(ParseNode $n) => $o->setTopProducts($n->getCollectionOfObjectValues([TopProductData::class, 'createFromDiscriminatorValue'])),
        ];
    }

    /**
     * Gets the orders property value. The orders property
     * @return int|null
    */
    public function getOrders(): ?int {
        return $this->orders;
    }

    /**
     * Gets the other_no_item_detail_cents property value. The other_no_item_detail_cents property
     * @return int|null
    */
    public function getOtherNoItemDetailCents(): ?int {
        return $this->other_no_item_detail_cents;
    }

    /**
     * Gets the revenue_cents property value. The revenue_cents property
     * @return int|null
    */
    public function getRevenueCents(): ?int {
        return $this->revenue_cents;
    }

    /**
     * Gets the top_products property value. The top_products property
     * @return array<TopProductData>|null
    */
    public function getTopProducts(): ?array {
        return $this->top_products;
    }

    /**
     * Serializes information the current object
     * @param SerializationWriter $writer Serialization writer to use to serialize this model
    */
    public function serialize(SerializationWriter $writer): void {
        $writer->writeFloatValue('average_items_per_order', $this->getAverageItemsPerOrder());
        $writer->writeCollectionOfObjectValues('categories', $this->getCategories());
        $writer->writeIntegerValue('orders', $this->getOrders());
        $writer->writeIntegerValue('other_no_item_detail_cents', $this->getOtherNoItemDetailCents());
        $writer->writeIntegerValue('revenue_cents', $this->getRevenueCents());
        $writer->writeCollectionOfObjectValues('top_products', $this->getTopProducts());
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
     * Sets the average_items_per_order property value. The average_items_per_order property
     * @param float|null $value Value to set for the average_items_per_order property.
    */
    public function setAverageItemsPerOrder(?float $value): void {
        $this->average_items_per_order = $value;
    }

    /**
     * Sets the categories property value. The categories property
     * @param array<CategoryRevenueData>|null $value Value to set for the categories property.
    */
    public function setCategories(?array $value): void {
        $this->categories = $value;
    }

    /**
     * Sets the orders property value. The orders property
     * @param int|null $value Value to set for the orders property.
    */
    public function setOrders(?int $value): void {
        $this->orders = $value;
    }

    /**
     * Sets the other_no_item_detail_cents property value. The other_no_item_detail_cents property
     * @param int|null $value Value to set for the other_no_item_detail_cents property.
    */
    public function setOtherNoItemDetailCents(?int $value): void {
        $this->other_no_item_detail_cents = $value;
    }

    /**
     * Sets the revenue_cents property value. The revenue_cents property
     * @param int|null $value Value to set for the revenue_cents property.
    */
    public function setRevenueCents(?int $value): void {
        $this->revenue_cents = $value;
    }

    /**
     * Sets the top_products property value. The top_products property
     * @param array<TopProductData>|null $value Value to set for the top_products property.
    */
    public function setTopProducts(?array $value): void {
        $this->top_products = $value;
    }

}
