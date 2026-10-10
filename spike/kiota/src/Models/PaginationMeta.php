<?php

namespace Puntjes\Spike\Kiota\Models;

use Microsoft\Kiota\Abstractions\Serialization\AdditionalDataHolder;
use Microsoft\Kiota\Abstractions\Serialization\Parsable;
use Microsoft\Kiota\Abstractions\Serialization\ParseNode;
use Microsoft\Kiota\Abstractions\Serialization\SerializationWriter;

class PaginationMeta implements AdditionalDataHolder, Parsable 
{
    /**
     * @var array<string, mixed>|null $additionalData Stores additional data not described in the OpenAPI description found when deserializing. Can be used for serialization as well.
    */
    private ?array $additionalData = null;
    
    /**
     * @var int|null $current_page The current_page property
    */
    private ?int $current_page = null;
    
    /**
     * @var int|null $last_page The last_page property
    */
    private ?int $last_page = null;
    
    /**
     * @var int|null $per_page The per_page property
    */
    private ?int $per_page = null;
    
    /**
     * @var int|null $total The total property
    */
    private ?int $total = null;
    
    /**
     * Instantiates a new PaginationMeta and sets the default values.
    */
    public function __construct() {
        $this->setAdditionalData([]);
    }

    /**
     * Creates a new instance of the appropriate class based on discriminator value
     * @param ParseNode $parseNode The parse node to use to read the discriminator value and create the object
     * @return PaginationMeta
    */
    public static function createFromDiscriminatorValue(ParseNode $parseNode): PaginationMeta {
        return new PaginationMeta();
    }

    /**
     * Gets the AdditionalData property value. Stores additional data not described in the OpenAPI description found when deserializing. Can be used for serialization as well.
     * @return array<string, mixed>|null
    */
    public function getAdditionalData(): ?array {
        return $this->additionalData;
    }

    /**
     * Gets the current_page property value. The current_page property
     * @return int|null
    */
    public function getCurrentPage(): ?int {
        return $this->current_page;
    }

    /**
     * The deserialization information for the current model
     * @return array<string, callable(ParseNode): void>
    */
    public function getFieldDeserializers(): array {
        $o = $this;
        return  [
            'current_page' => fn(ParseNode $n) => $o->setCurrentPage($n->getIntegerValue()),
            'last_page' => fn(ParseNode $n) => $o->setLastPage($n->getIntegerValue()),
            'per_page' => fn(ParseNode $n) => $o->setPerPage($n->getIntegerValue()),
            'total' => fn(ParseNode $n) => $o->setTotal($n->getIntegerValue()),
        ];
    }

    /**
     * Gets the last_page property value. The last_page property
     * @return int|null
    */
    public function getLastPage(): ?int {
        return $this->last_page;
    }

    /**
     * Gets the per_page property value. The per_page property
     * @return int|null
    */
    public function getPerPage(): ?int {
        return $this->per_page;
    }

    /**
     * Gets the total property value. The total property
     * @return int|null
    */
    public function getTotal(): ?int {
        return $this->total;
    }

    /**
     * Serializes information the current object
     * @param SerializationWriter $writer Serialization writer to use to serialize this model
    */
    public function serialize(SerializationWriter $writer): void {
        $writer->writeIntegerValue('current_page', $this->getCurrentPage());
        $writer->writeIntegerValue('last_page', $this->getLastPage());
        $writer->writeIntegerValue('per_page', $this->getPerPage());
        $writer->writeIntegerValue('total', $this->getTotal());
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
     * Sets the current_page property value. The current_page property
     * @param int|null $value Value to set for the current_page property.
    */
    public function setCurrentPage(?int $value): void {
        $this->current_page = $value;
    }

    /**
     * Sets the last_page property value. The last_page property
     * @param int|null $value Value to set for the last_page property.
    */
    public function setLastPage(?int $value): void {
        $this->last_page = $value;
    }

    /**
     * Sets the per_page property value. The per_page property
     * @param int|null $value Value to set for the per_page property.
    */
    public function setPerPage(?int $value): void {
        $this->per_page = $value;
    }

    /**
     * Sets the total property value. The total property
     * @param int|null $value Value to set for the total property.
    */
    public function setTotal(?int $value): void {
        $this->total = $value;
    }

}
