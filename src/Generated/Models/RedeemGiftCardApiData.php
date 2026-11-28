<?php

namespace Puntjes\Generated\Models;

use Microsoft\Kiota\Abstractions\Serialization\AdditionalDataHolder;
use Microsoft\Kiota\Abstractions\Serialization\Parsable;
use Microsoft\Kiota\Abstractions\Serialization\ParseNode;
use Microsoft\Kiota\Abstractions\Serialization\SerializationWriter;

class RedeemGiftCardApiData implements AdditionalDataHolder, Parsable 
{
    /**
     * @var array<string, mixed>|null $additionalData Stores additional data not described in the OpenAPI description found when deserializing. Can be used for serialization as well.
    */
    private ?array $additionalData = null;
    
    /**
     * @var int|null $amount_cents The amount_cents property
    */
    private ?int $amount_cents = null;
    
    /**
     * @var string|null $branch The branch property
    */
    private ?string $branch = null;
    
    /**
     * @var string|null $code The code property
    */
    private ?string $code = null;
    
    /**
     * @var string|null $idempotency_key The idempotency_key property
    */
    private ?string $idempotency_key = null;
    
    /**
     * Instantiates a new RedeemGiftCardApiData and sets the default values.
    */
    public function __construct() {
        $this->setAdditionalData([]);
    }

    /**
     * Creates a new instance of the appropriate class based on discriminator value
     * @param ParseNode $parseNode The parse node to use to read the discriminator value and create the object
     * @return RedeemGiftCardApiData
    */
    public static function createFromDiscriminatorValue(ParseNode $parseNode): RedeemGiftCardApiData {
        return new RedeemGiftCardApiData();
    }

    /**
     * Gets the AdditionalData property value. Stores additional data not described in the OpenAPI description found when deserializing. Can be used for serialization as well.
     * @return array<string, mixed>|null
    */
    public function getAdditionalData(): ?array {
        return $this->additionalData;
    }

    /**
     * Gets the amount_cents property value. The amount_cents property
     * @return int|null
    */
    public function getAmountCents(): ?int {
        return $this->amount_cents;
    }

    /**
     * Gets the branch property value. The branch property
     * @return string|null
    */
    public function getBranch(): ?string {
        return $this->branch;
    }

    /**
     * Gets the code property value. The code property
     * @return string|null
    */
    public function getCode(): ?string {
        return $this->code;
    }

    /**
     * The deserialization information for the current model
     * @return array<string, callable(ParseNode): void>
    */
    public function getFieldDeserializers(): array {
        $o = $this;
        return  [
            'amount_cents' => fn(ParseNode $n) => $o->setAmountCents($n->getIntegerValue()),
            'branch' => fn(ParseNode $n) => $o->setBranch($n->getStringValue()),
            'code' => fn(ParseNode $n) => $o->setCode($n->getStringValue()),
            'idempotency_key' => fn(ParseNode $n) => $o->setIdempotencyKey($n->getStringValue()),
        ];
    }

    /**
     * Gets the idempotency_key property value. The idempotency_key property
     * @return string|null
    */
    public function getIdempotencyKey(): ?string {
        return $this->idempotency_key;
    }

    /**
     * Serializes information the current object
     * @param SerializationWriter $writer Serialization writer to use to serialize this model
    */
    public function serialize(SerializationWriter $writer): void {
        $writer->writeIntegerValue('amount_cents', $this->getAmountCents());
        $writer->writeStringValue('branch', $this->getBranch());
        $writer->writeStringValue('code', $this->getCode());
        $writer->writeStringValue('idempotency_key', $this->getIdempotencyKey());
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
     * Sets the amount_cents property value. The amount_cents property
     * @param int|null $value Value to set for the amount_cents property.
    */
    public function setAmountCents(?int $value): void {
        $this->amount_cents = $value;
    }

    /**
     * Sets the branch property value. The branch property
     * @param string|null $value Value to set for the branch property.
    */
    public function setBranch(?string $value): void {
        $this->branch = $value;
    }

    /**
     * Sets the code property value. The code property
     * @param string|null $value Value to set for the code property.
    */
    public function setCode(?string $value): void {
        $this->code = $value;
    }

    /**
     * Sets the idempotency_key property value. The idempotency_key property
     * @param string|null $value Value to set for the idempotency_key property.
    */
    public function setIdempotencyKey(?string $value): void {
        $this->idempotency_key = $value;
    }

}
