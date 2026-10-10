<?php

namespace Puntjes\Spike\Kiota\Models;

use Microsoft\Kiota\Abstractions\Serialization\AdditionalDataHolder;
use Microsoft\Kiota\Abstractions\Serialization\Parsable;
use Microsoft\Kiota\Abstractions\Serialization\ParseNode;
use Microsoft\Kiota\Abstractions\Serialization\SerializationWriter;

class BulkUpsertSummaryData implements AdditionalDataHolder, Parsable 
{
    /**
     * @var array<string, mixed>|null $additionalData Stores additional data not described in the OpenAPI description found when deserializing. Can be used for serialization as well.
    */
    private ?array $additionalData = null;
    
    /**
     * @var int|null $created The created property
    */
    private ?int $created = null;
    
    /**
     * @var int|null $failed The failed property
    */
    private ?int $failed = null;
    
    /**
     * @var int|null $total The total property
    */
    private ?int $total = null;
    
    /**
     * @var int|null $updated The updated property
    */
    private ?int $updated = null;
    
    /**
     * Instantiates a new BulkUpsertSummaryData and sets the default values.
    */
    public function __construct() {
        $this->setAdditionalData([]);
    }

    /**
     * Creates a new instance of the appropriate class based on discriminator value
     * @param ParseNode $parseNode The parse node to use to read the discriminator value and create the object
     * @return BulkUpsertSummaryData
    */
    public static function createFromDiscriminatorValue(ParseNode $parseNode): BulkUpsertSummaryData {
        return new BulkUpsertSummaryData();
    }

    /**
     * Gets the AdditionalData property value. Stores additional data not described in the OpenAPI description found when deserializing. Can be used for serialization as well.
     * @return array<string, mixed>|null
    */
    public function getAdditionalData(): ?array {
        return $this->additionalData;
    }

    /**
     * Gets the created property value. The created property
     * @return int|null
    */
    public function getCreated(): ?int {
        return $this->created;
    }

    /**
     * Gets the failed property value. The failed property
     * @return int|null
    */
    public function getFailed(): ?int {
        return $this->failed;
    }

    /**
     * The deserialization information for the current model
     * @return array<string, callable(ParseNode): void>
    */
    public function getFieldDeserializers(): array {
        $o = $this;
        return  [
            'created' => fn(ParseNode $n) => $o->setCreated($n->getIntegerValue()),
            'failed' => fn(ParseNode $n) => $o->setFailed($n->getIntegerValue()),
            'total' => fn(ParseNode $n) => $o->setTotal($n->getIntegerValue()),
            'updated' => fn(ParseNode $n) => $o->setUpdated($n->getIntegerValue()),
        ];
    }

    /**
     * Gets the total property value. The total property
     * @return int|null
    */
    public function getTotal(): ?int {
        return $this->total;
    }

    /**
     * Gets the updated property value. The updated property
     * @return int|null
    */
    public function getUpdated(): ?int {
        return $this->updated;
    }

    /**
     * Serializes information the current object
     * @param SerializationWriter $writer Serialization writer to use to serialize this model
    */
    public function serialize(SerializationWriter $writer): void {
        $writer->writeIntegerValue('created', $this->getCreated());
        $writer->writeIntegerValue('failed', $this->getFailed());
        $writer->writeIntegerValue('total', $this->getTotal());
        $writer->writeIntegerValue('updated', $this->getUpdated());
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
     * Sets the created property value. The created property
     * @param int|null $value Value to set for the created property.
    */
    public function setCreated(?int $value): void {
        $this->created = $value;
    }

    /**
     * Sets the failed property value. The failed property
     * @param int|null $value Value to set for the failed property.
    */
    public function setFailed(?int $value): void {
        $this->failed = $value;
    }

    /**
     * Sets the total property value. The total property
     * @param int|null $value Value to set for the total property.
    */
    public function setTotal(?int $value): void {
        $this->total = $value;
    }

    /**
     * Sets the updated property value. The updated property
     * @param int|null $value Value to set for the updated property.
    */
    public function setUpdated(?int $value): void {
        $this->updated = $value;
    }

}
