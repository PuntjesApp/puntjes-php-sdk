<?php

namespace Puntjes\Spike\Kiota\Models;

use Microsoft\Kiota\Abstractions\Serialization\AdditionalDataHolder;
use Microsoft\Kiota\Abstractions\Serialization\Parsable;
use Microsoft\Kiota\Abstractions\Serialization\ParseNode;
use Microsoft\Kiota\Abstractions\Serialization\SerializationWriter;

class PaginationLinks implements AdditionalDataHolder, Parsable 
{
    /**
     * @var array<string, mixed>|null $additionalData Stores additional data not described in the OpenAPI description found when deserializing. Can be used for serialization as well.
    */
    private ?array $additionalData = null;
    
    /**
     * @var string|null $first The first property
    */
    private ?string $first = null;
    
    /**
     * @var string|null $last The last property
    */
    private ?string $last = null;
    
    /**
     * @var string|null $next The next property
    */
    private ?string $next = null;
    
    /**
     * @var string|null $prev The prev property
    */
    private ?string $prev = null;
    
    /**
     * Instantiates a new PaginationLinks and sets the default values.
    */
    public function __construct() {
        $this->setAdditionalData([]);
    }

    /**
     * Creates a new instance of the appropriate class based on discriminator value
     * @param ParseNode $parseNode The parse node to use to read the discriminator value and create the object
     * @return PaginationLinks
    */
    public static function createFromDiscriminatorValue(ParseNode $parseNode): PaginationLinks {
        return new PaginationLinks();
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
            'first' => fn(ParseNode $n) => $o->setFirst($n->getStringValue()),
            'last' => fn(ParseNode $n) => $o->setLast($n->getStringValue()),
            'next' => fn(ParseNode $n) => $o->setNext($n->getStringValue()),
            'prev' => fn(ParseNode $n) => $o->setPrev($n->getStringValue()),
        ];
    }

    /**
     * Gets the first property value. The first property
     * @return string|null
    */
    public function getFirst(): ?string {
        return $this->first;
    }

    /**
     * Gets the last property value. The last property
     * @return string|null
    */
    public function getLast(): ?string {
        return $this->last;
    }

    /**
     * Gets the next property value. The next property
     * @return string|null
    */
    public function getNext(): ?string {
        return $this->next;
    }

    /**
     * Gets the prev property value. The prev property
     * @return string|null
    */
    public function getPrev(): ?string {
        return $this->prev;
    }

    /**
     * Serializes information the current object
     * @param SerializationWriter $writer Serialization writer to use to serialize this model
    */
    public function serialize(SerializationWriter $writer): void {
        $writer->writeStringValue('first', $this->getFirst());
        $writer->writeStringValue('last', $this->getLast());
        $writer->writeStringValue('next', $this->getNext());
        $writer->writeStringValue('prev', $this->getPrev());
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
     * Sets the first property value. The first property
     * @param string|null $value Value to set for the first property.
    */
    public function setFirst(?string $value): void {
        $this->first = $value;
    }

    /**
     * Sets the last property value. The last property
     * @param string|null $value Value to set for the last property.
    */
    public function setLast(?string $value): void {
        $this->last = $value;
    }

    /**
     * Sets the next property value. The next property
     * @param string|null $value Value to set for the next property.
    */
    public function setNext(?string $value): void {
        $this->next = $value;
    }

    /**
     * Sets the prev property value. The prev property
     * @param string|null $value Value to set for the prev property.
    */
    public function setPrev(?string $value): void {
        $this->prev = $value;
    }

}
