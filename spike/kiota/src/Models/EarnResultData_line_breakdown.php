<?php

namespace Puntjes\Spike\Kiota\Models;

use Microsoft\Kiota\Abstractions\Serialization\AdditionalDataHolder;
use Microsoft\Kiota\Abstractions\Serialization\Parsable;
use Microsoft\Kiota\Abstractions\Serialization\ParseNode;
use Microsoft\Kiota\Abstractions\Serialization\SerializationWriter;

class EarnResultData_line_breakdown implements AdditionalDataHolder, Parsable 
{
    /**
     * @var array<string, mixed>|null $additionalData Stores additional data not described in the OpenAPI description found when deserializing. Can be used for serialization as well.
    */
    private ?array $additionalData = null;
    
    /**
     * @var int|null $points The points property
    */
    private ?int $points = null;
    
    /**
     * @var int|null $quantity The quantity property
    */
    private ?int $quantity = null;
    
    /**
     * @var string|null $sku The sku property
    */
    private ?string $sku = null;
    
    /**
     * Instantiates a new EarnResultData_line_breakdown and sets the default values.
    */
    public function __construct() {
        $this->setAdditionalData([]);
    }

    /**
     * Creates a new instance of the appropriate class based on discriminator value
     * @param ParseNode $parseNode The parse node to use to read the discriminator value and create the object
     * @return EarnResultData_line_breakdown
    */
    public static function createFromDiscriminatorValue(ParseNode $parseNode): EarnResultData_line_breakdown {
        return new EarnResultData_line_breakdown();
    }

    /**
     * Gets the AdditionalData property value. Stores additional data not described in the OpenAPI description found when deserializing. Can be used for serialization as well.
     * @return array<string, mixed>|null
    */
    public function getAdditionalData(): ?array {
        return $this->additionalData;
    }

    /**
     * The deserialization information for the current model
     * @return array<string, callable(ParseNode): void>
    */
    public function getFieldDeserializers(): array {
        $o = $this;
        return  [
            'points' => fn(ParseNode $n) => $o->setPoints($n->getIntegerValue()),
            'quantity' => fn(ParseNode $n) => $o->setQuantity($n->getIntegerValue()),
            'sku' => fn(ParseNode $n) => $o->setSku($n->getStringValue()),
        ];
    }

    /**
     * Gets the points property value. The points property
     * @return int|null
    */
    public function getPoints(): ?int {
        return $this->points;
    }

    /**
     * Gets the quantity property value. The quantity property
     * @return int|null
    */
    public function getQuantity(): ?int {
        return $this->quantity;
    }

    /**
     * Gets the sku property value. The sku property
     * @return string|null
    */
    public function getSku(): ?string {
        return $this->sku;
    }

    /**
     * Serializes information the current object
     * @param SerializationWriter $writer Serialization writer to use to serialize this model
    */
    public function serialize(SerializationWriter $writer): void {
        $writer->writeIntegerValue('points', $this->getPoints());
        $writer->writeIntegerValue('quantity', $this->getQuantity());
        $writer->writeStringValue('sku', $this->getSku());
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
     * Sets the points property value. The points property
     * @param int|null $value Value to set for the points property.
    */
    public function setPoints(?int $value): void {
        $this->points = $value;
    }

    /**
     * Sets the quantity property value. The quantity property
     * @param int|null $value Value to set for the quantity property.
    */
    public function setQuantity(?int $value): void {
        $this->quantity = $value;
    }

    /**
     * Sets the sku property value. The sku property
     * @param string|null $value Value to set for the sku property.
    */
    public function setSku(?string $value): void {
        $this->sku = $value;
    }

}
