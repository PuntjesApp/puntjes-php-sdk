<?php

namespace Puntjes\Spike\Kiota\Models;

use Microsoft\Kiota\Abstractions\Serialization\AdditionalDataHolder;
use Microsoft\Kiota\Abstractions\Serialization\Parsable;
use Microsoft\Kiota\Abstractions\Serialization\ParseNode;
use Microsoft\Kiota\Abstractions\Serialization\SerializationWriter;

class PointsGiftData implements AdditionalDataHolder, Parsable 
{
    /**
     * @var array<string, mixed>|null $additionalData Stores additional data not described in the OpenAPI description found when deserializing. Can be used for serialization as well.
    */
    private ?array $additionalData = null;
    
    /**
     * @var string|null $message A line the customer reads with the gift.
    */
    private ?string $message = null;
    
    /**
     * @var int|null $points How many points the customer gets.
    */
    private ?int $points = null;
    
    /**
     * @var PointsGiftData_type|null $type Always `points` for this shape.
    */
    private ?PointsGiftData_type $type = null;
    
    /**
     * Instantiates a new PointsGiftData and sets the default values.
    */
    public function __construct() {
        $this->setAdditionalData([]);
    }

    /**
     * Creates a new instance of the appropriate class based on discriminator value
     * @param ParseNode $parseNode The parse node to use to read the discriminator value and create the object
     * @return PointsGiftData
    */
    public static function createFromDiscriminatorValue(ParseNode $parseNode): PointsGiftData {
        return new PointsGiftData();
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
            'message' => fn(ParseNode $n) => $o->setMessage($n->getStringValue()),
            'points' => fn(ParseNode $n) => $o->setPoints($n->getIntegerValue()),
            'type' => fn(ParseNode $n) => $o->setType($n->getEnumValue(PointsGiftData_type::class)),
        ];
    }

    /**
     * Gets the message property value. A line the customer reads with the gift.
     * @return string|null
    */
    public function getMessage(): ?string {
        return $this->message;
    }

    /**
     * Gets the points property value. How many points the customer gets.
     * @return int|null
    */
    public function getPoints(): ?int {
        return $this->points;
    }

    /**
     * Gets the type property value. Always `points` for this shape.
     * @return PointsGiftData_type|null
    */
    public function getType(): ?PointsGiftData_type {
        return $this->type;
    }

    /**
     * Serializes information the current object
     * @param SerializationWriter $writer Serialization writer to use to serialize this model
    */
    public function serialize(SerializationWriter $writer): void {
        $writer->writeStringValue('message', $this->getMessage());
        $writer->writeIntegerValue('points', $this->getPoints());
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
     * Sets the message property value. A line the customer reads with the gift.
     * @param string|null $value Value to set for the message property.
    */
    public function setMessage(?string $value): void {
        $this->message = $value;
    }

    /**
     * Sets the points property value. How many points the customer gets.
     * @param int|null $value Value to set for the points property.
    */
    public function setPoints(?int $value): void {
        $this->points = $value;
    }

    /**
     * Sets the type property value. Always `points` for this shape.
     * @param PointsGiftData_type|null $value Value to set for the type property.
    */
    public function setType(?PointsGiftData_type $value): void {
        $this->type = $value;
    }

}
