<?php

namespace Puntjes\Spike\Kiota\Models;

use Microsoft\Kiota\Abstractions\Serialization\AdditionalDataHolder;
use Microsoft\Kiota\Abstractions\Serialization\Parsable;
use Microsoft\Kiota\Abstractions\Serialization\ParseNode;
use Microsoft\Kiota\Abstractions\Serialization\SerializationWriter;

class CreateCustomerData implements AdditionalDataHolder, Parsable 
{
    /**
     * @var array<string, mixed>|null $additionalData Stores additional data not described in the OpenAPI description found when deserializing. Can be used for serialization as well.
    */
    private ?array $additionalData = null;
    
    /**
     * @var string|null $customer_since The customer_since property
    */
    private ?string $customer_since = null;
    
    /**
     * @var string|null $date_of_birth The date_of_birth property
    */
    private ?string $date_of_birth = null;
    
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
     * @var array<CreateIdentifierData>|null $identifiers The identifiers property
    */
    private ?array $identifiers = null;
    
    /**
     * @var string|null $last_name The last_name property
    */
    private ?string $last_name = null;
    
    /**
     * @var CreateCustomerData_locale|null $locale The locale property
    */
    private ?CreateCustomerData_locale $locale = null;
    
    /**
     * @var bool|null $marketing_consent The marketing_consent property
    */
    private ?bool $marketing_consent = null;
    
    /**
     * @var string|null $marketing_consent_statement_version The marketing_consent_statement_version property
    */
    private ?string $marketing_consent_statement_version = null;
    
    /**
     * @var string|null $phone The phone property
    */
    private ?string $phone = null;
    
    /**
     * Instantiates a new CreateCustomerData and sets the default values.
    */
    public function __construct() {
        $this->setAdditionalData([]);
    }

    /**
     * Creates a new instance of the appropriate class based on discriminator value
     * @param ParseNode $parseNode The parse node to use to read the discriminator value and create the object
     * @return CreateCustomerData
    */
    public static function createFromDiscriminatorValue(ParseNode $parseNode): CreateCustomerData {
        return new CreateCustomerData();
    }

    /**
     * Gets the AdditionalData property value. Stores additional data not described in the OpenAPI description found when deserializing. Can be used for serialization as well.
     * @return array<string, mixed>|null
    */
    public function getAdditionalData(): ?array {
        return $this->additionalData;
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
            'customer_since' => fn(ParseNode $n) => $o->setCustomerSince($n->getStringValue()),
            'date_of_birth' => fn(ParseNode $n) => $o->setDateOfBirth($n->getStringValue()),
            'email' => fn(ParseNode $n) => $o->setEmail($n->getStringValue()),
            'external_id' => fn(ParseNode $n) => $o->setExternalId($n->getStringValue()),
            'first_name' => fn(ParseNode $n) => $o->setFirstName($n->getStringValue()),
            'identifiers' => fn(ParseNode $n) => $o->setIdentifiers($n->getCollectionOfObjectValues([CreateIdentifierData::class, 'createFromDiscriminatorValue'])),
            'last_name' => fn(ParseNode $n) => $o->setLastName($n->getStringValue()),
            'locale' => fn(ParseNode $n) => $o->setLocale($n->getEnumValue(CreateCustomerData_locale::class)),
            'marketing_consent' => fn(ParseNode $n) => $o->setMarketingConsent($n->getBooleanValue()),
            'marketing_consent_statement_version' => fn(ParseNode $n) => $o->setMarketingConsentStatementVersion($n->getStringValue()),
            'phone' => fn(ParseNode $n) => $o->setPhone($n->getStringValue()),
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
     * Gets the identifiers property value. The identifiers property
     * @return array<CreateIdentifierData>|null
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
     * @return CreateCustomerData_locale|null
    */
    public function getLocale(): ?CreateCustomerData_locale {
        return $this->locale;
    }

    /**
     * Gets the marketing_consent property value. The marketing_consent property
     * @return bool|null
    */
    public function getMarketingConsent(): ?bool {
        return $this->marketing_consent;
    }

    /**
     * Gets the marketing_consent_statement_version property value. The marketing_consent_statement_version property
     * @return string|null
    */
    public function getMarketingConsentStatementVersion(): ?string {
        return $this->marketing_consent_statement_version;
    }

    /**
     * Gets the phone property value. The phone property
     * @return string|null
    */
    public function getPhone(): ?string {
        return $this->phone;
    }

    /**
     * Serializes information the current object
     * @param SerializationWriter $writer Serialization writer to use to serialize this model
    */
    public function serialize(SerializationWriter $writer): void {
        $writer->writeStringValue('customer_since', $this->getCustomerSince());
        $writer->writeStringValue('date_of_birth', $this->getDateOfBirth());
        $writer->writeStringValue('email', $this->getEmail());
        $writer->writeStringValue('external_id', $this->getExternalId());
        $writer->writeStringValue('first_name', $this->getFirstName());
        $writer->writeCollectionOfObjectValues('identifiers', $this->getIdentifiers());
        $writer->writeStringValue('last_name', $this->getLastName());
        $writer->writeEnumValue('locale', $this->getLocale());
        $writer->writeBooleanValue('marketing_consent', $this->getMarketingConsent());
        $writer->writeStringValue('marketing_consent_statement_version', $this->getMarketingConsentStatementVersion());
        $writer->writeStringValue('phone', $this->getPhone());
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
     * Sets the identifiers property value. The identifiers property
     * @param array<CreateIdentifierData>|null $value Value to set for the identifiers property.
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
     * @param CreateCustomerData_locale|null $value Value to set for the locale property.
    */
    public function setLocale(?CreateCustomerData_locale $value): void {
        $this->locale = $value;
    }

    /**
     * Sets the marketing_consent property value. The marketing_consent property
     * @param bool|null $value Value to set for the marketing_consent property.
    */
    public function setMarketingConsent(?bool $value): void {
        $this->marketing_consent = $value;
    }

    /**
     * Sets the marketing_consent_statement_version property value. The marketing_consent_statement_version property
     * @param string|null $value Value to set for the marketing_consent_statement_version property.
    */
    public function setMarketingConsentStatementVersion(?string $value): void {
        $this->marketing_consent_statement_version = $value;
    }

    /**
     * Sets the phone property value. The phone property
     * @param string|null $value Value to set for the phone property.
    */
    public function setPhone(?string $value): void {
        $this->phone = $value;
    }

}
