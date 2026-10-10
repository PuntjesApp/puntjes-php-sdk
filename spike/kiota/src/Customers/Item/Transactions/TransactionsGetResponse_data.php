<?php

namespace Puntjes\Spike\Kiota\Customers\Item\Transactions;

use Microsoft\Kiota\Abstractions\Serialization\AdditionalDataHolder;
use Microsoft\Kiota\Abstractions\Serialization\Parsable;
use Microsoft\Kiota\Abstractions\Serialization\ParseNode;
use Microsoft\Kiota\Abstractions\Serialization\SerializationWriter;
use Puntjes\Spike\Kiota\Models\PaginationLinks;
use Puntjes\Spike\Kiota\Models\PaginationMeta;
use Puntjes\Spike\Kiota\Models\TransactionData;

class TransactionsGetResponse_data implements AdditionalDataHolder, Parsable 
{
    /**
     * @var array<string, mixed>|null $additionalData Stores additional data not described in the OpenAPI description found when deserializing. Can be used for serialization as well.
    */
    private ?array $additionalData = null;
    
    /**
     * @var array<TransactionData>|null $data The data property
    */
    private ?array $data = null;
    
    /**
     * @var PaginationLinks|null $links The links property
    */
    private ?PaginationLinks $links = null;
    
    /**
     * @var PaginationMeta|null $meta The meta property
    */
    private ?PaginationMeta $meta = null;
    
    /**
     * Instantiates a new TransactionsGetResponse_data and sets the default values.
    */
    public function __construct() {
        $this->setAdditionalData([]);
    }

    /**
     * Creates a new instance of the appropriate class based on discriminator value
     * @param ParseNode $parseNode The parse node to use to read the discriminator value and create the object
     * @return TransactionsGetResponse_data
    */
    public static function createFromDiscriminatorValue(ParseNode $parseNode): TransactionsGetResponse_data {
        return new TransactionsGetResponse_data();
    }

    /**
     * Gets the AdditionalData property value. Stores additional data not described in the OpenAPI description found when deserializing. Can be used for serialization as well.
     * @return array<string, mixed>|null
    */
    public function getAdditionalData(): ?array {
        return $this->additionalData;
    }

    /**
     * Gets the data property value. The data property
     * @return array<TransactionData>|null
    */
    public function getData(): ?array {
        return $this->data;
    }

    /**
     * The deserialization information for the current model
     * @return array<string, callable(ParseNode): void>
    */
    public function getFieldDeserializers(): array {
        $o = $this;
        return  [
            'data' => fn(ParseNode $n) => $o->setData($n->getCollectionOfObjectValues([TransactionData::class, 'createFromDiscriminatorValue'])),
            'links' => fn(ParseNode $n) => $o->setLinks($n->getObjectValue([PaginationLinks::class, 'createFromDiscriminatorValue'])),
            'meta' => fn(ParseNode $n) => $o->setMeta($n->getObjectValue([PaginationMeta::class, 'createFromDiscriminatorValue'])),
        ];
    }

    /**
     * Gets the links property value. The links property
     * @return PaginationLinks|null
    */
    public function getLinks(): ?PaginationLinks {
        return $this->links;
    }

    /**
     * Gets the meta property value. The meta property
     * @return PaginationMeta|null
    */
    public function getMeta(): ?PaginationMeta {
        return $this->meta;
    }

    /**
     * Serializes information the current object
     * @param SerializationWriter $writer Serialization writer to use to serialize this model
    */
    public function serialize(SerializationWriter $writer): void {
        $writer->writeCollectionOfObjectValues('data', $this->getData());
        $writer->writeObjectValue('links', $this->getLinks());
        $writer->writeObjectValue('meta', $this->getMeta());
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
     * Sets the data property value. The data property
     * @param array<TransactionData>|null $value Value to set for the data property.
    */
    public function setData(?array $value): void {
        $this->data = $value;
    }

    /**
     * Sets the links property value. The links property
     * @param PaginationLinks|null $value Value to set for the links property.
    */
    public function setLinks(?PaginationLinks $value): void {
        $this->links = $value;
    }

    /**
     * Sets the meta property value. The meta property
     * @param PaginationMeta|null $value Value to set for the meta property.
    */
    public function setMeta(?PaginationMeta $value): void {
        $this->meta = $value;
    }

}
