<?php

namespace Puntjes\Spike\Kiota\Models;

use Microsoft\Kiota\Abstractions\Serialization\AdditionalDataHolder;
use Microsoft\Kiota\Abstractions\Serialization\Parsable;
use Microsoft\Kiota\Abstractions\Serialization\ParseNode;
use Microsoft\Kiota\Abstractions\Serialization\SerializationWriter;

class BulkUpsertResultData implements AdditionalDataHolder, Parsable 
{
    /**
     * @var array<string, mixed>|null $additionalData Stores additional data not described in the OpenAPI description found when deserializing. Can be used for serialization as well.
    */
    private ?array $additionalData = null;
    
    /**
     * @var BulkUpsertResultData_errors|null $errors Each rejected field and its messages. Absent when the item succeeded.
    */
    private ?BulkUpsertResultData_errors $errors = null;
    
    /**
     * @var string|null $external_id The external_id property
    */
    private ?string $external_id = null;
    
    /**
     * @var int|null $index The index property
    */
    private ?int $index = null;
    
    /**
     * @var ProductData|null $product The product property
    */
    private ?ProductData $product = null;
    
    /**
     * @var string|null $status The status property
    */
    private ?string $status = null;
    
    /**
     * Instantiates a new BulkUpsertResultData and sets the default values.
    */
    public function __construct() {
        $this->setAdditionalData([]);
    }

    /**
     * Creates a new instance of the appropriate class based on discriminator value
     * @param ParseNode $parseNode The parse node to use to read the discriminator value and create the object
     * @return BulkUpsertResultData
    */
    public static function createFromDiscriminatorValue(ParseNode $parseNode): BulkUpsertResultData {
        return new BulkUpsertResultData();
    }

    /**
     * Gets the AdditionalData property value. Stores additional data not described in the OpenAPI description found when deserializing. Can be used for serialization as well.
     * @return array<string, mixed>|null
    */
    public function getAdditionalData(): ?array {
        return $this->additionalData;
    }

    /**
     * Gets the errors property value. Each rejected field and its messages. Absent when the item succeeded.
     * @return BulkUpsertResultData_errors|null
    */
    public function getErrors(): ?BulkUpsertResultData_errors {
        return $this->errors;
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
            'errors' => fn(ParseNode $n) => $o->setErrors($n->getObjectValue([BulkUpsertResultData_errors::class, 'createFromDiscriminatorValue'])),
            'external_id' => fn(ParseNode $n) => $o->setExternalId($n->getStringValue()),
            'index' => fn(ParseNode $n) => $o->setIndex($n->getIntegerValue()),
            'product' => fn(ParseNode $n) => $o->setProduct($n->getObjectValue([ProductData::class, 'createFromDiscriminatorValue'])),
            'status' => fn(ParseNode $n) => $o->setStatus($n->getStringValue()),
        ];
    }

    /**
     * Gets the index property value. The index property
     * @return int|null
    */
    public function getIndex(): ?int {
        return $this->index;
    }

    /**
     * Gets the product property value. The product property
     * @return ProductData|null
    */
    public function getProduct(): ?ProductData {
        return $this->product;
    }

    /**
     * Gets the status property value. The status property
     * @return string|null
    */
    public function getStatus(): ?string {
        return $this->status;
    }

    /**
     * Serializes information the current object
     * @param SerializationWriter $writer Serialization writer to use to serialize this model
    */
    public function serialize(SerializationWriter $writer): void {
        $writer->writeObjectValue('errors', $this->getErrors());
        $writer->writeStringValue('external_id', $this->getExternalId());
        $writer->writeIntegerValue('index', $this->getIndex());
        $writer->writeObjectValue('product', $this->getProduct());
        $writer->writeStringValue('status', $this->getStatus());
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
     * Sets the errors property value. Each rejected field and its messages. Absent when the item succeeded.
     * @param BulkUpsertResultData_errors|null $value Value to set for the errors property.
    */
    public function setErrors(?BulkUpsertResultData_errors $value): void {
        $this->errors = $value;
    }

    /**
     * Sets the external_id property value. The external_id property
     * @param string|null $value Value to set for the external_id property.
    */
    public function setExternalId(?string $value): void {
        $this->external_id = $value;
    }

    /**
     * Sets the index property value. The index property
     * @param int|null $value Value to set for the index property.
    */
    public function setIndex(?int $value): void {
        $this->index = $value;
    }

    /**
     * Sets the product property value. The product property
     * @param ProductData|null $value Value to set for the product property.
    */
    public function setProduct(?ProductData $value): void {
        $this->product = $value;
    }

    /**
     * Sets the status property value. The status property
     * @param string|null $value Value to set for the status property.
    */
    public function setStatus(?string $value): void {
        $this->status = $value;
    }

}
