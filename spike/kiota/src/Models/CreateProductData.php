<?php

namespace Puntjes\Spike\Kiota\Models;

use Microsoft\Kiota\Abstractions\Serialization\AdditionalDataHolder;
use Microsoft\Kiota\Abstractions\Serialization\Parsable;
use Microsoft\Kiota\Abstractions\Serialization\ParseNode;
use Microsoft\Kiota\Abstractions\Serialization\SerializationWriter;

class CreateProductData implements AdditionalDataHolder, Parsable 
{
    /**
     * @var array<string, mixed>|null $additionalData Stores additional data not described in the OpenAPI description found when deserializing. Can be used for serialization as well.
    */
    private ?array $additionalData = null;
    
    /**
     * @var string|null $category The category property
    */
    private ?string $category = null;
    
    /**
     * @var string|null $description The description property
    */
    private ?string $description = null;
    
    /**
     * @var string|null $external_id The external_id property
    */
    private ?string $external_id = null;
    
    /**
     * @var string|null $image_url The image_url property
    */
    private ?string $image_url = null;
    
    /**
     * @var CreateProductData_metadata|null $metadata The metadata property
    */
    private ?CreateProductData_metadata $metadata = null;
    
    /**
     * @var string|null $name The name property
    */
    private ?string $name = null;
    
    /**
     * @var int|null $price_cents The price_cents property
    */
    private ?int $price_cents = null;
    
    /**
     * @var CreateProductData_status|null $status The status property
    */
    private ?CreateProductData_status $status = null;
    
    /**
     * @var int|null $stock The stock property
    */
    private ?int $stock = null;
    
    /**
     * Instantiates a new CreateProductData and sets the default values.
    */
    public function __construct() {
        $this->setAdditionalData([]);
    }

    /**
     * Creates a new instance of the appropriate class based on discriminator value
     * @param ParseNode $parseNode The parse node to use to read the discriminator value and create the object
     * @return CreateProductData
    */
    public static function createFromDiscriminatorValue(ParseNode $parseNode): CreateProductData {
        return new CreateProductData();
    }

    /**
     * Gets the AdditionalData property value. Stores additional data not described in the OpenAPI description found when deserializing. Can be used for serialization as well.
     * @return array<string, mixed>|null
    */
    public function getAdditionalData(): ?array {
        return $this->additionalData;
    }

    /**
     * Gets the category property value. The category property
     * @return string|null
    */
    public function getCategory(): ?string {
        return $this->category;
    }

    /**
     * Gets the description property value. The description property
     * @return string|null
    */
    public function getDescription(): ?string {
        return $this->description;
    }

    /**
     * Gets the external_id property value. The external_id property
     * @return string|null
    */
    public function getExternalId(): ?string {
        return $this->external_id;
    }

    /**
     * The deserialization information for the current model
     * @return array<string, callable(ParseNode): void>
    */
    public function getFieldDeserializers(): array {
        $o = $this;
        return  [
            'category' => fn(ParseNode $n) => $o->setCategory($n->getStringValue()),
            'description' => fn(ParseNode $n) => $o->setDescription($n->getStringValue()),
            'external_id' => fn(ParseNode $n) => $o->setExternalId($n->getStringValue()),
            'image_url' => fn(ParseNode $n) => $o->setImageUrl($n->getStringValue()),
            'metadata' => fn(ParseNode $n) => $o->setMetadata($n->getObjectValue([CreateProductData_metadata::class, 'createFromDiscriminatorValue'])),
            'name' => fn(ParseNode $n) => $o->setName($n->getStringValue()),
            'price_cents' => fn(ParseNode $n) => $o->setPriceCents($n->getIntegerValue()),
            'status' => fn(ParseNode $n) => $o->setStatus($n->getEnumValue(CreateProductData_status::class)),
            'stock' => fn(ParseNode $n) => $o->setStock($n->getIntegerValue()),
        ];
    }

    /**
     * Gets the image_url property value. The image_url property
     * @return string|null
    */
    public function getImageUrl(): ?string {
        return $this->image_url;
    }

    /**
     * Gets the metadata property value. The metadata property
     * @return CreateProductData_metadata|null
    */
    public function getMetadata(): ?CreateProductData_metadata {
        return $this->metadata;
    }

    /**
     * Gets the name property value. The name property
     * @return string|null
    */
    public function getName(): ?string {
        return $this->name;
    }

    /**
     * Gets the price_cents property value. The price_cents property
     * @return int|null
    */
    public function getPriceCents(): ?int {
        return $this->price_cents;
    }

    /**
     * Gets the status property value. The status property
     * @return CreateProductData_status|null
    */
    public function getStatus(): ?CreateProductData_status {
        return $this->status;
    }

    /**
     * Gets the stock property value. The stock property
     * @return int|null
    */
    public function getStock(): ?int {
        return $this->stock;
    }

    /**
     * Serializes information the current object
     * @param SerializationWriter $writer Serialization writer to use to serialize this model
    */
    public function serialize(SerializationWriter $writer): void {
        $writer->writeStringValue('category', $this->getCategory());
        $writer->writeStringValue('description', $this->getDescription());
        $writer->writeStringValue('external_id', $this->getExternalId());
        $writer->writeStringValue('image_url', $this->getImageUrl());
        $writer->writeObjectValue('metadata', $this->getMetadata());
        $writer->writeStringValue('name', $this->getName());
        $writer->writeIntegerValue('price_cents', $this->getPriceCents());
        $writer->writeEnumValue('status', $this->getStatus());
        $writer->writeIntegerValue('stock', $this->getStock());
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
     * Sets the category property value. The category property
     * @param string|null $value Value to set for the category property.
    */
    public function setCategory(?string $value): void {
        $this->category = $value;
    }

    /**
     * Sets the description property value. The description property
     * @param string|null $value Value to set for the description property.
    */
    public function setDescription(?string $value): void {
        $this->description = $value;
    }

    /**
     * Sets the external_id property value. The external_id property
     * @param string|null $value Value to set for the external_id property.
    */
    public function setExternalId(?string $value): void {
        $this->external_id = $value;
    }

    /**
     * Sets the image_url property value. The image_url property
     * @param string|null $value Value to set for the image_url property.
    */
    public function setImageUrl(?string $value): void {
        $this->image_url = $value;
    }

    /**
     * Sets the metadata property value. The metadata property
     * @param CreateProductData_metadata|null $value Value to set for the metadata property.
    */
    public function setMetadata(?CreateProductData_metadata $value): void {
        $this->metadata = $value;
    }

    /**
     * Sets the name property value. The name property
     * @param string|null $value Value to set for the name property.
    */
    public function setName(?string $value): void {
        $this->name = $value;
    }

    /**
     * Sets the price_cents property value. The price_cents property
     * @param int|null $value Value to set for the price_cents property.
    */
    public function setPriceCents(?int $value): void {
        $this->price_cents = $value;
    }

    /**
     * Sets the status property value. The status property
     * @param CreateProductData_status|null $value Value to set for the status property.
    */
    public function setStatus(?CreateProductData_status $value): void {
        $this->status = $value;
    }

    /**
     * Sets the stock property value. The stock property
     * @param int|null $value Value to set for the stock property.
    */
    public function setStock(?int $value): void {
        $this->stock = $value;
    }

}
