<?php

namespace Puntjes\Generated\Models;

use Microsoft\Kiota\Abstractions\Serialization\AdditionalDataHolder;
use Microsoft\Kiota\Abstractions\Serialization\Parsable;
use Microsoft\Kiota\Abstractions\Serialization\ParseNode;
use Microsoft\Kiota\Abstractions\Serialization\SerializationWriter;

class MergeCustomersApiData implements AdditionalDataHolder, Parsable 
{
    /**
     * @var bool|null $add_points `true` adds the points of the closed account to the kept account; `false` drops them.
    */
    private ?bool $add_points = null;
    
    /**
     * @var array<string, mixed>|null $additionalData Stores additional data not described in the OpenAPI description found when deserializing. Can be used for serialization as well.
    */
    private ?array $additionalData = null;
    
    /**
     * @var string|null $idempotency_key Max 255 characters; the same request again answers 200, another is refused.
    */
    private ?string $idempotency_key = null;
    
    /**
     * @var int|null $source Id of the closed account; its rows go to the kept account, its points too if `add_points`.
    */
    private ?int $source = null;
    
    /**
     * Instantiates a new MergeCustomersApiData and sets the default values.
    */
    public function __construct() {
        $this->setAdditionalData([]);
    }

    /**
     * Creates a new instance of the appropriate class based on discriminator value
     * @param ParseNode $parseNode The parse node to use to read the discriminator value and create the object
     * @return MergeCustomersApiData
    */
    public static function createFromDiscriminatorValue(ParseNode $parseNode): MergeCustomersApiData {
        return new MergeCustomersApiData();
    }

    /**
     * Gets the AdditionalData property value. Stores additional data not described in the OpenAPI description found when deserializing. Can be used for serialization as well.
     * @return array<string, mixed>|null
    */
    public function getAdditionalData(): ?array {
        return $this->additionalData;
    }

    /**
     * Gets the add_points property value. `true` adds the points of the closed account to the kept account; `false` drops them.
     * @return bool|null
    */
    public function getAddPoints(): ?bool {
        return $this->add_points;
    }

    /**
     * The deserialization information for the current model
     * @return array<string, callable(ParseNode): void>
    */
    public function getFieldDeserializers(): array {
        $o = $this;
        return  [
            'add_points' => fn(ParseNode $n) => $o->setAddPoints($n->getBooleanValue()),
            'idempotency_key' => fn(ParseNode $n) => $o->setIdempotencyKey($n->getStringValue()),
            'source' => fn(ParseNode $n) => $o->setSource($n->getIntegerValue()),
        ];
    }

    /**
     * Gets the idempotency_key property value. Max 255 characters; the same request again answers 200, another is refused.
     * @return string|null
    */
    public function getIdempotencyKey(): ?string {
        return $this->idempotency_key;
    }

    /**
     * Gets the source property value. Id of the closed account; its rows go to the kept account, its points too if `add_points`.
     * @return int|null
    */
    public function getSource(): ?int {
        return $this->source;
    }

    /**
     * Serializes information the current object
     * @param SerializationWriter $writer Serialization writer to use to serialize this model
    */
    public function serialize(SerializationWriter $writer): void {
        $writer->writeBooleanValue('add_points', $this->getAddPoints());
        $writer->writeStringValue('idempotency_key', $this->getIdempotencyKey());
        $writer->writeIntegerValue('source', $this->getSource());
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
     * Sets the add_points property value. `true` adds the points of the closed account to the kept account; `false` drops them.
     * @param bool|null $value Value to set for the add_points property.
    */
    public function setAddPoints(?bool $value): void {
        $this->add_points = $value;
    }

    /**
     * Sets the idempotency_key property value. Max 255 characters; the same request again answers 200, another is refused.
     * @param string|null $value Value to set for the idempotency_key property.
    */
    public function setIdempotencyKey(?string $value): void {
        $this->idempotency_key = $value;
    }

    /**
     * Sets the source property value. Id of the closed account; its rows go to the kept account, its points too if `add_points`.
     * @param int|null $value Value to set for the source property.
    */
    public function setSource(?int $value): void {
        $this->source = $value;
    }

}
