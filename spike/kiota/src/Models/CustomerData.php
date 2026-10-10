<?php

namespace Puntjes\Spike\Kiota\Models;

use Microsoft\Kiota\Abstractions\Serialization\AdditionalDataHolder;
use Microsoft\Kiota\Abstractions\Serialization\Parsable;
use Microsoft\Kiota\Abstractions\Serialization\ParseNode;
use Microsoft\Kiota\Abstractions\Serialization\SerializationWriter;

class CustomerData implements AdditionalDataHolder, Parsable 
{
    /**
     * @var array<string, mixed>|null $additionalData Stores additional data not described in the OpenAPI description found when deserializing. Can be used for serialization as well.
    */
    private ?array $additionalData = null;
    
    /**
     * @var string|null $anonymized_at The anonymized_at property
    */
    private ?string $anonymized_at = null;
    
    /**
     * @var string|null $created_at The created_at property
    */
    private ?string $created_at = null;
    
    /**
     * @var string|null $customer_since The customer_since property
    */
    private ?string $customer_since = null;
    
    /**
     * @var string|null $date_of_birth The date_of_birth property
    */
    private ?string $date_of_birth = null;
    
    /**
     * @var string|null $deactivated_at The deactivated_at property
    */
    private ?string $deactivated_at = null;
    
    /**
     * @var string|null $email The email property
    */
    private ?string $email = null;
    
    /**
     * @var string|null $external_id The external_id property
    */
    private ?string $external_id = null;
    
    /**
     * @var string|null $first_name The first_name property
    */
    private ?string $first_name = null;
    
    /**
     * @var int|null $id The id property
    */
    private ?int $id = null;
    
    /**
     * @var array<CustomerIdentifierData>|null $identifiers The identifiers property
    */
    private ?array $identifiers = null;
    
    /**
     * @var string|null $last_name The last_name property
    */
    private ?string $last_name = null;
    
    /**
     * @var string|null $locale The locale property
    */
    private ?string $locale = null;
    
    /**
     * @var string|null $loyalty_card_code The loyalty_card_code property
    */
    private ?string $loyalty_card_code = null;
    
    /**
     * @var bool|null $marketingConsent The marketingConsent property
    */
    private ?bool $marketingConsent = null;
    
    /**
     * @var string|null $marketingConsentGrantedAt The marketingConsentGrantedAt property
    */
    private ?string $marketingConsentGrantedAt = null;
    
    /**
     * @var string|null $marketingConsentGrantedSource The marketingConsentGrantedSource property
    */
    private ?string $marketingConsentGrantedSource = null;
    
    /**
     * @var string|null $marketingConsentRecordedBy The marketingConsentRecordedBy property
    */
    private ?string $marketingConsentRecordedBy = null;
    
    /**
     * @var string|null $marketingConsentRecordedByClientId The marketingConsentRecordedByClientId property
    */
    private ?string $marketingConsentRecordedByClientId = null;
    
    /**
     * @var string|null $marketingConsentStatementVersion The marketingConsentStatementVersion property
    */
    private ?string $marketingConsentStatementVersion = null;
    
    /**
     * @var string|null $marketingConsentWithdrawnAt The marketingConsentWithdrawnAt property
    */
    private ?string $marketingConsentWithdrawnAt = null;
    
    /**
     * @var string|null $marketingConsentWithdrawnSource The marketingConsentWithdrawnSource property
    */
    private ?string $marketingConsentWithdrawnSource = null;
    
    /**
     * @var string|null $phone The phone property
    */
    private ?string $phone = null;
    
    /**
     * @var CustomerStatusData|null $status The status property
    */
    private ?CustomerStatusData $status = null;
    
    /**
     * @var string|null $updated_at The updated_at property
    */
    private ?string $updated_at = null;
    
    /**
     * Instantiates a new CustomerData and sets the default values.
    */
    public function __construct() {
        $this->setAdditionalData([]);
    }

    /**
     * Creates a new instance of the appropriate class based on discriminator value
     * @param ParseNode $parseNode The parse node to use to read the discriminator value and create the object
     * @return CustomerData
    */
    public static function createFromDiscriminatorValue(ParseNode $parseNode): CustomerData {
        return new CustomerData();
    }

    /**
     * Gets the AdditionalData property value. Stores additional data not described in the OpenAPI description found when deserializing. Can be used for serialization as well.
     * @return array<string, mixed>|null
    */
    public function getAdditionalData(): ?array {
        return $this->additionalData;
    }

    /**
     * Gets the anonymized_at property value. The anonymized_at property
     * @return string|null
    */
    public function getAnonymizedAt(): ?string {
        return $this->anonymized_at;
    }

    /**
     * Gets the created_at property value. The created_at property
     * @return string|null
    */
    public function getCreatedAt(): ?string {
        return $this->created_at;
    }

    /**
     * Gets the customer_since property value. The customer_since property
     * @return string|null
    */
    public function getCustomerSince(): ?string {
        return $this->customer_since;
    }

    /**
     * Gets the date_of_birth property value. The date_of_birth property
     * @return string|null
    */
    public function getDateOfBirth(): ?string {
        return $this->date_of_birth;
    }

    /**
     * Gets the deactivated_at property value. The deactivated_at property
     * @return string|null
    */
    public function getDeactivatedAt(): ?string {
        return $this->deactivated_at;
    }

    /**
     * Gets the email property value. The email property
     * @return string|null
    */
    public function getEmail(): ?string {
        return $this->email;
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
            'anonymized_at' => fn(ParseNode $n) => $o->setAnonymizedAt($n->getStringValue()),
            'created_at' => fn(ParseNode $n) => $o->setCreatedAt($n->getStringValue()),
            'customer_since' => fn(ParseNode $n) => $o->setCustomerSince($n->getStringValue()),
            'date_of_birth' => fn(ParseNode $n) => $o->setDateOfBirth($n->getStringValue()),
            'deactivated_at' => fn(ParseNode $n) => $o->setDeactivatedAt($n->getStringValue()),
            'email' => fn(ParseNode $n) => $o->setEmail($n->getStringValue()),
            'external_id' => fn(ParseNode $n) => $o->setExternalId($n->getStringValue()),
            'first_name' => fn(ParseNode $n) => $o->setFirstName($n->getStringValue()),
            'id' => fn(ParseNode $n) => $o->setId($n->getIntegerValue()),
            'identifiers' => fn(ParseNode $n) => $o->setIdentifiers($n->getCollectionOfObjectValues([CustomerIdentifierData::class, 'createFromDiscriminatorValue'])),
            'last_name' => fn(ParseNode $n) => $o->setLastName($n->getStringValue()),
            'locale' => fn(ParseNode $n) => $o->setLocale($n->getStringValue()),
            'loyalty_card_code' => fn(ParseNode $n) => $o->setLoyaltyCardCode($n->getStringValue()),
            'marketingConsent' => fn(ParseNode $n) => $o->setMarketingConsent($n->getBooleanValue()),
            'marketingConsentGrantedAt' => fn(ParseNode $n) => $o->setMarketingConsentGrantedAt($n->getStringValue()),
            'marketingConsentGrantedSource' => fn(ParseNode $n) => $o->setMarketingConsentGrantedSource($n->getStringValue()),
            'marketingConsentRecordedBy' => fn(ParseNode $n) => $o->setMarketingConsentRecordedBy($n->getStringValue()),
            'marketingConsentRecordedByClientId' => fn(ParseNode $n) => $o->setMarketingConsentRecordedByClientId($n->getStringValue()),
            'marketingConsentStatementVersion' => fn(ParseNode $n) => $o->setMarketingConsentStatementVersion($n->getStringValue()),
            'marketingConsentWithdrawnAt' => fn(ParseNode $n) => $o->setMarketingConsentWithdrawnAt($n->getStringValue()),
            'marketingConsentWithdrawnSource' => fn(ParseNode $n) => $o->setMarketingConsentWithdrawnSource($n->getStringValue()),
            'phone' => fn(ParseNode $n) => $o->setPhone($n->getStringValue()),
            'status' => fn(ParseNode $n) => $o->setStatus($n->getObjectValue([CustomerStatusData::class, 'createFromDiscriminatorValue'])),
            'updated_at' => fn(ParseNode $n) => $o->setUpdatedAt($n->getStringValue()),
        ];
    }

    /**
     * Gets the first_name property value. The first_name property
     * @return string|null
    */
    public function getFirstName(): ?string {
        return $this->first_name;
    }

    /**
     * Gets the id property value. The id property
     * @return int|null
    */
    public function getId(): ?int {
        return $this->id;
    }

    /**
     * Gets the identifiers property value. The identifiers property
     * @return array<CustomerIdentifierData>|null
    */
    public function getIdentifiers(): ?array {
        return $this->identifiers;
    }

    /**
     * Gets the last_name property value. The last_name property
     * @return string|null
    */
    public function getLastName(): ?string {
        return $this->last_name;
    }

    /**
     * Gets the locale property value. The locale property
     * @return string|null
    */
    public function getLocale(): ?string {
        return $this->locale;
    }

    /**
     * Gets the loyalty_card_code property value. The loyalty_card_code property
     * @return string|null
    */
    public function getLoyaltyCardCode(): ?string {
        return $this->loyalty_card_code;
    }

    /**
     * Gets the marketingConsent property value. The marketingConsent property
     * @return bool|null
    */
    public function getMarketingConsent(): ?bool {
        return $this->marketingConsent;
    }

    /**
     * Gets the marketingConsentGrantedAt property value. The marketingConsentGrantedAt property
     * @return string|null
    */
    public function getMarketingConsentGrantedAt(): ?string {
        return $this->marketingConsentGrantedAt;
    }

    /**
     * Gets the marketingConsentGrantedSource property value. The marketingConsentGrantedSource property
     * @return string|null
    */
    public function getMarketingConsentGrantedSource(): ?string {
        return $this->marketingConsentGrantedSource;
    }

    /**
     * Gets the marketingConsentRecordedBy property value. The marketingConsentRecordedBy property
     * @return string|null
    */
    public function getMarketingConsentRecordedBy(): ?string {
        return $this->marketingConsentRecordedBy;
    }

    /**
     * Gets the marketingConsentRecordedByClientId property value. The marketingConsentRecordedByClientId property
     * @return string|null
    */
    public function getMarketingConsentRecordedByClientId(): ?string {
        return $this->marketingConsentRecordedByClientId;
    }

    /**
     * Gets the marketingConsentStatementVersion property value. The marketingConsentStatementVersion property
     * @return string|null
    */
    public function getMarketingConsentStatementVersion(): ?string {
        return $this->marketingConsentStatementVersion;
    }

    /**
     * Gets the marketingConsentWithdrawnAt property value. The marketingConsentWithdrawnAt property
     * @return string|null
    */
    public function getMarketingConsentWithdrawnAt(): ?string {
        return $this->marketingConsentWithdrawnAt;
    }

    /**
     * Gets the marketingConsentWithdrawnSource property value. The marketingConsentWithdrawnSource property
     * @return string|null
    */
    public function getMarketingConsentWithdrawnSource(): ?string {
        return $this->marketingConsentWithdrawnSource;
    }

    /**
     * Gets the phone property value. The phone property
     * @return string|null
    */
    public function getPhone(): ?string {
        return $this->phone;
    }

    /**
     * Gets the status property value. The status property
     * @return CustomerStatusData|null
    */
    public function getStatus(): ?CustomerStatusData {
        return $this->status;
    }

    /**
     * Gets the updated_at property value. The updated_at property
     * @return string|null
    */
    public function getUpdatedAt(): ?string {
        return $this->updated_at;
    }

    /**
     * Serializes information the current object
     * @param SerializationWriter $writer Serialization writer to use to serialize this model
    */
    public function serialize(SerializationWriter $writer): void {
        $writer->writeStringValue('anonymized_at', $this->getAnonymizedAt());
        $writer->writeStringValue('created_at', $this->getCreatedAt());
        $writer->writeStringValue('customer_since', $this->getCustomerSince());
        $writer->writeStringValue('date_of_birth', $this->getDateOfBirth());
        $writer->writeStringValue('deactivated_at', $this->getDeactivatedAt());
        $writer->writeStringValue('email', $this->getEmail());
        $writer->writeStringValue('external_id', $this->getExternalId());
        $writer->writeStringValue('first_name', $this->getFirstName());
        $writer->writeIntegerValue('id', $this->getId());
        $writer->writeCollectionOfObjectValues('identifiers', $this->getIdentifiers());
        $writer->writeStringValue('last_name', $this->getLastName());
        $writer->writeStringValue('locale', $this->getLocale());
        $writer->writeStringValue('loyalty_card_code', $this->getLoyaltyCardCode());
        $writer->writeBooleanValue('marketingConsent', $this->getMarketingConsent());
        $writer->writeStringValue('marketingConsentGrantedAt', $this->getMarketingConsentGrantedAt());
        $writer->writeStringValue('marketingConsentGrantedSource', $this->getMarketingConsentGrantedSource());
        $writer->writeStringValue('marketingConsentRecordedBy', $this->getMarketingConsentRecordedBy());
        $writer->writeStringValue('marketingConsentRecordedByClientId', $this->getMarketingConsentRecordedByClientId());
        $writer->writeStringValue('marketingConsentStatementVersion', $this->getMarketingConsentStatementVersion());
        $writer->writeStringValue('marketingConsentWithdrawnAt', $this->getMarketingConsentWithdrawnAt());
        $writer->writeStringValue('marketingConsentWithdrawnSource', $this->getMarketingConsentWithdrawnSource());
        $writer->writeStringValue('phone', $this->getPhone());
        $writer->writeObjectValue('status', $this->getStatus());
        $writer->writeStringValue('updated_at', $this->getUpdatedAt());
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
     * Sets the anonymized_at property value. The anonymized_at property
     * @param string|null $value Value to set for the anonymized_at property.
    */
    public function setAnonymizedAt(?string $value): void {
        $this->anonymized_at = $value;
    }

    /**
     * Sets the created_at property value. The created_at property
     * @param string|null $value Value to set for the created_at property.
    */
    public function setCreatedAt(?string $value): void {
        $this->created_at = $value;
    }

    /**
     * Sets the customer_since property value. The customer_since property
     * @param string|null $value Value to set for the customer_since property.
    */
    public function setCustomerSince(?string $value): void {
        $this->customer_since = $value;
    }

    /**
     * Sets the date_of_birth property value. The date_of_birth property
     * @param string|null $value Value to set for the date_of_birth property.
    */
    public function setDateOfBirth(?string $value): void {
        $this->date_of_birth = $value;
    }

    /**
     * Sets the deactivated_at property value. The deactivated_at property
     * @param string|null $value Value to set for the deactivated_at property.
    */
    public function setDeactivatedAt(?string $value): void {
        $this->deactivated_at = $value;
    }

    /**
     * Sets the email property value. The email property
     * @param string|null $value Value to set for the email property.
    */
    public function setEmail(?string $value): void {
        $this->email = $value;
    }

    /**
     * Sets the external_id property value. The external_id property
     * @param string|null $value Value to set for the external_id property.
    */
    public function setExternalId(?string $value): void {
        $this->external_id = $value;
    }

    /**
     * Sets the first_name property value. The first_name property
     * @param string|null $value Value to set for the first_name property.
    */
    public function setFirstName(?string $value): void {
        $this->first_name = $value;
    }

    /**
     * Sets the id property value. The id property
     * @param int|null $value Value to set for the id property.
    */
    public function setId(?int $value): void {
        $this->id = $value;
    }

    /**
     * Sets the identifiers property value. The identifiers property
     * @param array<CustomerIdentifierData>|null $value Value to set for the identifiers property.
    */
    public function setIdentifiers(?array $value): void {
        $this->identifiers = $value;
    }

    /**
     * Sets the last_name property value. The last_name property
     * @param string|null $value Value to set for the last_name property.
    */
    public function setLastName(?string $value): void {
        $this->last_name = $value;
    }

    /**
     * Sets the locale property value. The locale property
     * @param string|null $value Value to set for the locale property.
    */
    public function setLocale(?string $value): void {
        $this->locale = $value;
    }

    /**
     * Sets the loyalty_card_code property value. The loyalty_card_code property
     * @param string|null $value Value to set for the loyalty_card_code property.
    */
    public function setLoyaltyCardCode(?string $value): void {
        $this->loyalty_card_code = $value;
    }

    /**
     * Sets the marketingConsent property value. The marketingConsent property
     * @param bool|null $value Value to set for the marketingConsent property.
    */
    public function setMarketingConsent(?bool $value): void {
        $this->marketingConsent = $value;
    }

    /**
     * Sets the marketingConsentGrantedAt property value. The marketingConsentGrantedAt property
     * @param string|null $value Value to set for the marketingConsentGrantedAt property.
    */
    public function setMarketingConsentGrantedAt(?string $value): void {
        $this->marketingConsentGrantedAt = $value;
    }

    /**
     * Sets the marketingConsentGrantedSource property value. The marketingConsentGrantedSource property
     * @param string|null $value Value to set for the marketingConsentGrantedSource property.
    */
    public function setMarketingConsentGrantedSource(?string $value): void {
        $this->marketingConsentGrantedSource = $value;
    }

    /**
     * Sets the marketingConsentRecordedBy property value. The marketingConsentRecordedBy property
     * @param string|null $value Value to set for the marketingConsentRecordedBy property.
    */
    public function setMarketingConsentRecordedBy(?string $value): void {
        $this->marketingConsentRecordedBy = $value;
    }

    /**
     * Sets the marketingConsentRecordedByClientId property value. The marketingConsentRecordedByClientId property
     * @param string|null $value Value to set for the marketingConsentRecordedByClientId property.
    */
    public function setMarketingConsentRecordedByClientId(?string $value): void {
        $this->marketingConsentRecordedByClientId = $value;
    }

    /**
     * Sets the marketingConsentStatementVersion property value. The marketingConsentStatementVersion property
     * @param string|null $value Value to set for the marketingConsentStatementVersion property.
    */
    public function setMarketingConsentStatementVersion(?string $value): void {
        $this->marketingConsentStatementVersion = $value;
    }

    /**
     * Sets the marketingConsentWithdrawnAt property value. The marketingConsentWithdrawnAt property
     * @param string|null $value Value to set for the marketingConsentWithdrawnAt property.
    */
    public function setMarketingConsentWithdrawnAt(?string $value): void {
        $this->marketingConsentWithdrawnAt = $value;
    }

    /**
     * Sets the marketingConsentWithdrawnSource property value. The marketingConsentWithdrawnSource property
     * @param string|null $value Value to set for the marketingConsentWithdrawnSource property.
    */
    public function setMarketingConsentWithdrawnSource(?string $value): void {
        $this->marketingConsentWithdrawnSource = $value;
    }

    /**
     * Sets the phone property value. The phone property
     * @param string|null $value Value to set for the phone property.
    */
    public function setPhone(?string $value): void {
        $this->phone = $value;
    }

    /**
     * Sets the status property value. The status property
     * @param CustomerStatusData|null $value Value to set for the status property.
    */
    public function setStatus(?CustomerStatusData $value): void {
        $this->status = $value;
    }

    /**
     * Sets the updated_at property value. The updated_at property
     * @param string|null $value Value to set for the updated_at property.
    */
    public function setUpdatedAt(?string $value): void {
        $this->updated_at = $value;
    }

}
