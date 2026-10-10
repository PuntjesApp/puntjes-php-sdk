<?php

namespace Puntjes\Spike\Kiota\Models;

use Microsoft\Kiota\Abstractions\Serialization\AdditionalDataHolder;
use Microsoft\Kiota\Abstractions\Serialization\Parsable;
use Microsoft\Kiota\Abstractions\Serialization\ParseNode;
use Microsoft\Kiota\Abstractions\Serialization\SerializationWriter;

class ErrorResponse_error implements AdditionalDataHolder, Parsable 
{
    /**
     * @var array<string, mixed>|null $additionalData Stores additional data not described in the OpenAPI description found when deserializing. Can be used for serialization as well.
    */
    private ?array $additionalData = null;
    
    /**
     * @var string|null $code The machine-readable code to branch on.
    */
    private ?string $code = null;
    
    /**
     * @var ErrorResponse_error_details|null $details Extra context for this failure. A validation error puts each rejected field and its messages here.
    */
    private ?ErrorResponse_error_details $details = null;
    
    /**
     * @var string|null $message What went wrong, in words.
    */
    private ?string $message = null;
    
    /**
     * @var string|null $request_id Identifies this request in our logs. Quote it when you report a problem.
    */
    private ?string $request_id = null;
    
    /**
     * @var int|null $status The HTTP status, repeated in the body.
    */
    private ?int $status = null;
    
    /**
     * Instantiates a new ErrorResponse_error and sets the default values.
    */
    public function __construct() {
        $this->setAdditionalData([]);
    }

    /**
     * Creates a new instance of the appropriate class based on discriminator value
     * @param ParseNode $parseNode The parse node to use to read the discriminator value and create the object
     * @return ErrorResponse_error
    */
    public static function createFromDiscriminatorValue(ParseNode $parseNode): ErrorResponse_error {
        return new ErrorResponse_error();
    }

    /**
     * Gets the AdditionalData property value. Stores additional data not described in the OpenAPI description found when deserializing. Can be used for serialization as well.
     * @return array<string, mixed>|null
    */
    public function getAdditionalData(): ?array {
        return $this->additionalData;
    }

    /**
     * Gets the code property value. The machine-readable code to branch on.
     * @return string|null
    */
    public function getCode(): ?string {
        return $this->code;
    }

    /**
     * Gets the details property value. Extra context for this failure. A validation error puts each rejected field and its messages here.
     * @return ErrorResponse_error_details|null
    */
    public function getDetails(): ?ErrorResponse_error_details {
        return $this->details;
    }

    /**
     * The deserialization information for the current model
     * @return array<string, callable(ParseNode): void>
    */
    public function getFieldDeserializers(): array {
        $o = $this;
        return  [
            'code' => fn(ParseNode $n) => $o->setCode($n->getStringValue()),
            'details' => fn(ParseNode $n) => $o->setDetails($n->getObjectValue([ErrorResponse_error_details::class, 'createFromDiscriminatorValue'])),
            'message' => fn(ParseNode $n) => $o->setMessage($n->getStringValue()),
            'request_id' => fn(ParseNode $n) => $o->setRequestId($n->getStringValue()),
            'status' => fn(ParseNode $n) => $o->setStatus($n->getIntegerValue()),
        ];
    }

    /**
     * Gets the message property value. What went wrong, in words.
     * @return string|null
    */
    public function getMessage(): ?string {
        return $this->message;
    }

    /**
     * Gets the request_id property value. Identifies this request in our logs. Quote it when you report a problem.
     * @return string|null
    */
    public function getRequestId(): ?string {
        return $this->request_id;
    }

    /**
     * Gets the status property value. The HTTP status, repeated in the body.
     * @return int|null
    */
    public function getStatus(): ?int {
        return $this->status;
    }

    /**
     * Serializes information the current object
     * @param SerializationWriter $writer Serialization writer to use to serialize this model
    */
    public function serialize(SerializationWriter $writer): void {
        $writer->writeStringValue('code', $this->getCode());
        $writer->writeObjectValue('details', $this->getDetails());
        $writer->writeStringValue('message', $this->getMessage());
        $writer->writeStringValue('request_id', $this->getRequestId());
        $writer->writeIntegerValue('status', $this->getStatus());
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
     * Sets the code property value. The machine-readable code to branch on.
     * @param string|null $value Value to set for the code property.
    */
    public function setCode(?string $value): void {
        $this->code = $value;
    }

    /**
     * Sets the details property value. Extra context for this failure. A validation error puts each rejected field and its messages here.
     * @param ErrorResponse_error_details|null $value Value to set for the details property.
    */
    public function setDetails(?ErrorResponse_error_details $value): void {
        $this->details = $value;
    }

    /**
     * Sets the message property value. What went wrong, in words.
     * @param string|null $value Value to set for the message property.
    */
    public function setMessage(?string $value): void {
        $this->message = $value;
    }

    /**
     * Sets the request_id property value. Identifies this request in our logs. Quote it when you report a problem.
     * @param string|null $value Value to set for the request_id property.
    */
    public function setRequestId(?string $value): void {
        $this->request_id = $value;
    }

    /**
     * Sets the status property value. The HTTP status, repeated in the body.
     * @param int|null $value Value to set for the status property.
    */
    public function setStatus(?int $value): void {
        $this->status = $value;
    }

}
