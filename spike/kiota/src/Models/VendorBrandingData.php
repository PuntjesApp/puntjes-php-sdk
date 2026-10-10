<?php

namespace Puntjes\Spike\Kiota\Models;

use Microsoft\Kiota\Abstractions\Serialization\AdditionalDataHolder;
use Microsoft\Kiota\Abstractions\Serialization\Parsable;
use Microsoft\Kiota\Abstractions\Serialization\ParseNode;
use Microsoft\Kiota\Abstractions\Serialization\SerializationWriter;

class VendorBrandingData implements AdditionalDataHolder, Parsable 
{
    /**
     * @var array<string, mixed>|null $additionalData Stores additional data not described in the OpenAPI description found when deserializing. Can be used for serialization as well.
    */
    private ?array $additionalData = null;
    
    /**
     * @var string|null $brand_color_accent The brand_color_accent property
    */
    private ?string $brand_color_accent = null;
    
    /**
     * @var string|null $brand_color_primary The brand_color_primary property
    */
    private ?string $brand_color_primary = null;
    
    /**
     * @var string|null $display_name The display_name property
    */
    private ?string $display_name = null;
    
    /**
     * @var string|null $logo_url The logo_url property
    */
    private ?string $logo_url = null;
    
    /**
     * Instantiates a new VendorBrandingData and sets the default values.
    */
    public function __construct() {
        $this->setAdditionalData([]);
    }

    /**
     * Creates a new instance of the appropriate class based on discriminator value
     * @param ParseNode $parseNode The parse node to use to read the discriminator value and create the object
     * @return VendorBrandingData
    */
    public static function createFromDiscriminatorValue(ParseNode $parseNode): VendorBrandingData {
        return new VendorBrandingData();
    }

    /**
     * Gets the AdditionalData property value. Stores additional data not described in the OpenAPI description found when deserializing. Can be used for serialization as well.
     * @return array<string, mixed>|null
    */
    public function getAdditionalData(): ?array {
        return $this->additionalData;
    }

    /**
     * Gets the brand_color_accent property value. The brand_color_accent property
     * @return string|null
    */
    public function getBrandColorAccent(): ?string {
        return $this->brand_color_accent;
    }

    /**
     * Gets the brand_color_primary property value. The brand_color_primary property
     * @return string|null
    */
    public function getBrandColorPrimary(): ?string {
        return $this->brand_color_primary;
    }

    /**
     * Gets the display_name property value. The display_name property
     * @return string|null
    */
    public function getDisplayName(): ?string {
        return $this->display_name;
    }

    /**
     * The deserialization information for the current model
     * @return array<string, callable(ParseNode): void>
    */
    public function getFieldDeserializers(): array {
        $o = $this;
        return  [
            'brand_color_accent' => fn(ParseNode $n) => $o->setBrandColorAccent($n->getStringValue()),
            'brand_color_primary' => fn(ParseNode $n) => $o->setBrandColorPrimary($n->getStringValue()),
            'display_name' => fn(ParseNode $n) => $o->setDisplayName($n->getStringValue()),
            'logo_url' => fn(ParseNode $n) => $o->setLogoUrl($n->getStringValue()),
        ];
    }

    /**
     * Gets the logo_url property value. The logo_url property
     * @return string|null
    */
    public function getLogoUrl(): ?string {
        return $this->logo_url;
    }

    /**
     * Serializes information the current object
     * @param SerializationWriter $writer Serialization writer to use to serialize this model
    */
    public function serialize(SerializationWriter $writer): void {
        $writer->writeStringValue('brand_color_accent', $this->getBrandColorAccent());
        $writer->writeStringValue('brand_color_primary', $this->getBrandColorPrimary());
        $writer->writeStringValue('display_name', $this->getDisplayName());
        $writer->writeStringValue('logo_url', $this->getLogoUrl());
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
     * Sets the brand_color_accent property value. The brand_color_accent property
     * @param string|null $value Value to set for the brand_color_accent property.
    */
    public function setBrandColorAccent(?string $value): void {
        $this->brand_color_accent = $value;
    }

    /**
     * Sets the brand_color_primary property value. The brand_color_primary property
     * @param string|null $value Value to set for the brand_color_primary property.
    */
    public function setBrandColorPrimary(?string $value): void {
        $this->brand_color_primary = $value;
    }

    /**
     * Sets the display_name property value. The display_name property
     * @param string|null $value Value to set for the display_name property.
    */
    public function setDisplayName(?string $value): void {
        $this->display_name = $value;
    }

    /**
     * Sets the logo_url property value. The logo_url property
     * @param string|null $value Value to set for the logo_url property.
    */
    public function setLogoUrl(?string $value): void {
        $this->logo_url = $value;
    }

}
