<?php

namespace Puntjes\Spike\Kiota\Models;

use Microsoft\Kiota\Abstractions\Serialization\AdditionalDataHolder;
use Microsoft\Kiota\Abstractions\Serialization\Parsable;
use Microsoft\Kiota\Abstractions\Serialization\ParseNode;
use Microsoft\Kiota\Abstractions\Serialization\SerializationWriter;

class BulkUpsertResponseData implements AdditionalDataHolder, Parsable 
{
    /**
     * @var array<string, mixed>|null $additionalData Stores additional data not described in the OpenAPI description found when deserializing. Can be used for serialization as well.
    */
    private ?array $additionalData = null;
    
    /**
     * @var array<BulkUpsertResultData>|null $results The results property
    */
    private ?array $results = null;
    
    /**
     * @var BulkUpsertSummaryData|null $summary The summary property
    */
    private ?BulkUpsertSummaryData $summary = null;
    
    /**
     * Instantiates a new BulkUpsertResponseData and sets the default values.
    */
    public function __construct() {
        $this->setAdditionalData([]);
    }

    /**
     * Creates a new instance of the appropriate class based on discriminator value
     * @param ParseNode $parseNode The parse node to use to read the discriminator value and create the object
     * @return BulkUpsertResponseData
    */
    public static function createFromDiscriminatorValue(ParseNode $parseNode): BulkUpsertResponseData {
        return new BulkUpsertResponseData();
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
            'results' => fn(ParseNode $n) => $o->setResults($n->getCollectionOfObjectValues([BulkUpsertResultData::class, 'createFromDiscriminatorValue'])),
            'summary' => fn(ParseNode $n) => $o->setSummary($n->getObjectValue([BulkUpsertSummaryData::class, 'createFromDiscriminatorValue'])),
        ];
    }

    /**
     * Gets the results property value. The results property
     * @return array<BulkUpsertResultData>|null
    */
    public function getResults(): ?array {
        return $this->results;
    }

    /**
     * Gets the summary property value. The summary property
     * @return BulkUpsertSummaryData|null
    */
    public function getSummary(): ?BulkUpsertSummaryData {
        return $this->summary;
    }

    /**
     * Serializes information the current object
     * @param SerializationWriter $writer Serialization writer to use to serialize this model
    */
    public function serialize(SerializationWriter $writer): void {
        $writer->writeCollectionOfObjectValues('results', $this->getResults());
        $writer->writeObjectValue('summary', $this->getSummary());
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
     * Sets the results property value. The results property
     * @param array<BulkUpsertResultData>|null $value Value to set for the results property.
    */
    public function setResults(?array $value): void {
        $this->results = $value;
    }

    /**
     * Sets the summary property value. The summary property
     * @param BulkUpsertSummaryData|null $value Value to set for the summary property.
    */
    public function setSummary(?BulkUpsertSummaryData $value): void {
        $this->summary = $value;
    }

}
