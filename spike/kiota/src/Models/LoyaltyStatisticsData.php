<?php

namespace Puntjes\Spike\Kiota\Models;

use Microsoft\Kiota\Abstractions\Serialization\AdditionalDataHolder;
use Microsoft\Kiota\Abstractions\Serialization\Parsable;
use Microsoft\Kiota\Abstractions\Serialization\ParseNode;
use Microsoft\Kiota\Abstractions\Serialization\SerializationWriter;

class LoyaltyStatisticsData implements AdditionalDataHolder, Parsable 
{
    /**
     * @var array<string, mixed>|null $additionalData Stores additional data not described in the OpenAPI description found when deserializing. Can be used for serialization as well.
    */
    private ?array $additionalData = null;
    
    /**
     * @var float|null $breakage_rate points_expired divided by points_issued, 4 decimals. Counts imported points when they expire but not when they arrive, so it can pass 1.0 after an import. Use breakage_rate_excluding_import.
    */
    private ?float $breakage_rate = null;
    
    /**
     * @var float|null $breakage_rate_excluding_import Points expired minus points_expired_from_import, divided by points_issued, 4 decimals; null when nothing was issued. The rate the dashboard shows.
    */
    private ?float $breakage_rate_excluding_import = null;
    
    /**
     * @var int|null $net_adjustments The net_adjustments property
    */
    private ?int $net_adjustments = null;
    
    /**
     * @var int|null $points_expired Points that expired in the period, including points customers brought along from another loyalty system.
    */
    private ?int $points_expired = null;
    
    /**
     * @var int|null $points_expired_from_import The part of points_expired that came from points customers brought along from another loyalty system.
    */
    private ?int $points_expired_from_import = null;
    
    /**
     * @var int|null $points_issued The points_issued property
    */
    private ?int $points_issued = null;
    
    /**
     * @var int|null $points_redeemed Points spent on rewards in the period, including points customers brought along from another loyalty system.
    */
    private ?int $points_redeemed = null;
    
    /**
     * @var int|null $points_redeemed_from_import The part of points_redeemed that came from points customers brought along from another loyalty system.
    */
    private ?int $points_redeemed_from_import = null;
    
    /**
     * @var float|null $redemption_rate points_redeemed divided by points_issued, 3 decimals. Counts imported points when they are spent but not when they arrive, so it can pass 1.0 after an import. Use redemption_rate_excluding_import.
    */
    private ?float $redemption_rate = null;
    
    /**
     * @var float|null $redemption_rate_excluding_import Points redeemed minus points_redeemed_from_import, divided by points_issued, 3 decimals; null when nothing was issued. The rate the dashboard shows.
    */
    private ?float $redemption_rate_excluding_import = null;
    
    /**
     * Instantiates a new LoyaltyStatisticsData and sets the default values.
    */
    public function __construct() {
        $this->setAdditionalData([]);
    }

    /**
     * Creates a new instance of the appropriate class based on discriminator value
     * @param ParseNode $parseNode The parse node to use to read the discriminator value and create the object
     * @return LoyaltyStatisticsData
    */
    public static function createFromDiscriminatorValue(ParseNode $parseNode): LoyaltyStatisticsData {
        return new LoyaltyStatisticsData();
    }

    /**
     * Gets the AdditionalData property value. Stores additional data not described in the OpenAPI description found when deserializing. Can be used for serialization as well.
     * @return array<string, mixed>|null
    */
    public function getAdditionalData(): ?array {
        return $this->additionalData;
    }

    /**
     * Gets the breakage_rate property value. points_expired divided by points_issued, 4 decimals. Counts imported points when they expire but not when they arrive, so it can pass 1.0 after an import. Use breakage_rate_excluding_import.
     * @return float|null
    */
    public function getBreakageRate(): ?float {
        return $this->breakage_rate;
    }

    /**
     * Gets the breakage_rate_excluding_import property value. Points expired minus points_expired_from_import, divided by points_issued, 4 decimals; null when nothing was issued. The rate the dashboard shows.
     * @return float|null
    */
    public function getBreakageRateExcludingImport(): ?float {
        return $this->breakage_rate_excluding_import;
    }

    /**
     * The deserialization information for the current model
     * @return array<string, callable(ParseNode): void>
    */
    public function getFieldDeserializers(): array {
        $o = $this;
        return  [
            'breakage_rate' => fn(ParseNode $n) => $o->setBreakageRate($n->getFloatValue()),
            'breakage_rate_excluding_import' => fn(ParseNode $n) => $o->setBreakageRateExcludingImport($n->getFloatValue()),
            'net_adjustments' => fn(ParseNode $n) => $o->setNetAdjustments($n->getIntegerValue()),
            'points_expired' => fn(ParseNode $n) => $o->setPointsExpired($n->getIntegerValue()),
            'points_expired_from_import' => fn(ParseNode $n) => $o->setPointsExpiredFromImport($n->getIntegerValue()),
            'points_issued' => fn(ParseNode $n) => $o->setPointsIssued($n->getIntegerValue()),
            'points_redeemed' => fn(ParseNode $n) => $o->setPointsRedeemed($n->getIntegerValue()),
            'points_redeemed_from_import' => fn(ParseNode $n) => $o->setPointsRedeemedFromImport($n->getIntegerValue()),
            'redemption_rate' => fn(ParseNode $n) => $o->setRedemptionRate($n->getFloatValue()),
            'redemption_rate_excluding_import' => fn(ParseNode $n) => $o->setRedemptionRateExcludingImport($n->getFloatValue()),
        ];
    }

    /**
     * Gets the net_adjustments property value. The net_adjustments property
     * @return int|null
    */
    public function getNetAdjustments(): ?int {
        return $this->net_adjustments;
    }

    /**
     * Gets the points_expired property value. Points that expired in the period, including points customers brought along from another loyalty system.
     * @return int|null
    */
    public function getPointsExpired(): ?int {
        return $this->points_expired;
    }

    /**
     * Gets the points_expired_from_import property value. The part of points_expired that came from points customers brought along from another loyalty system.
     * @return int|null
    */
    public function getPointsExpiredFromImport(): ?int {
        return $this->points_expired_from_import;
    }

    /**
     * Gets the points_issued property value. The points_issued property
     * @return int|null
    */
    public function getPointsIssued(): ?int {
        return $this->points_issued;
    }

    /**
     * Gets the points_redeemed property value. Points spent on rewards in the period, including points customers brought along from another loyalty system.
     * @return int|null
    */
    public function getPointsRedeemed(): ?int {
        return $this->points_redeemed;
    }

    /**
     * Gets the points_redeemed_from_import property value. The part of points_redeemed that came from points customers brought along from another loyalty system.
     * @return int|null
    */
    public function getPointsRedeemedFromImport(): ?int {
        return $this->points_redeemed_from_import;
    }

    /**
     * Gets the redemption_rate property value. points_redeemed divided by points_issued, 3 decimals. Counts imported points when they are spent but not when they arrive, so it can pass 1.0 after an import. Use redemption_rate_excluding_import.
     * @return float|null
    */
    public function getRedemptionRate(): ?float {
        return $this->redemption_rate;
    }

    /**
     * Gets the redemption_rate_excluding_import property value. Points redeemed minus points_redeemed_from_import, divided by points_issued, 3 decimals; null when nothing was issued. The rate the dashboard shows.
     * @return float|null
    */
    public function getRedemptionRateExcludingImport(): ?float {
        return $this->redemption_rate_excluding_import;
    }

    /**
     * Serializes information the current object
     * @param SerializationWriter $writer Serialization writer to use to serialize this model
    */
    public function serialize(SerializationWriter $writer): void {
        $writer->writeFloatValue('breakage_rate', $this->getBreakageRate());
        $writer->writeFloatValue('breakage_rate_excluding_import', $this->getBreakageRateExcludingImport());
        $writer->writeIntegerValue('net_adjustments', $this->getNetAdjustments());
        $writer->writeIntegerValue('points_expired', $this->getPointsExpired());
        $writer->writeIntegerValue('points_expired_from_import', $this->getPointsExpiredFromImport());
        $writer->writeIntegerValue('points_issued', $this->getPointsIssued());
        $writer->writeIntegerValue('points_redeemed', $this->getPointsRedeemed());
        $writer->writeIntegerValue('points_redeemed_from_import', $this->getPointsRedeemedFromImport());
        $writer->writeFloatValue('redemption_rate', $this->getRedemptionRate());
        $writer->writeFloatValue('redemption_rate_excluding_import', $this->getRedemptionRateExcludingImport());
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
     * Sets the breakage_rate property value. points_expired divided by points_issued, 4 decimals. Counts imported points when they expire but not when they arrive, so it can pass 1.0 after an import. Use breakage_rate_excluding_import.
     * @param float|null $value Value to set for the breakage_rate property.
    */
    public function setBreakageRate(?float $value): void {
        $this->breakage_rate = $value;
    }

    /**
     * Sets the breakage_rate_excluding_import property value. Points expired minus points_expired_from_import, divided by points_issued, 4 decimals; null when nothing was issued. The rate the dashboard shows.
     * @param float|null $value Value to set for the breakage_rate_excluding_import property.
    */
    public function setBreakageRateExcludingImport(?float $value): void {
        $this->breakage_rate_excluding_import = $value;
    }

    /**
     * Sets the net_adjustments property value. The net_adjustments property
     * @param int|null $value Value to set for the net_adjustments property.
    */
    public function setNetAdjustments(?int $value): void {
        $this->net_adjustments = $value;
    }

    /**
     * Sets the points_expired property value. Points that expired in the period, including points customers brought along from another loyalty system.
     * @param int|null $value Value to set for the points_expired property.
    */
    public function setPointsExpired(?int $value): void {
        $this->points_expired = $value;
    }

    /**
     * Sets the points_expired_from_import property value. The part of points_expired that came from points customers brought along from another loyalty system.
     * @param int|null $value Value to set for the points_expired_from_import property.
    */
    public function setPointsExpiredFromImport(?int $value): void {
        $this->points_expired_from_import = $value;
    }

    /**
     * Sets the points_issued property value. The points_issued property
     * @param int|null $value Value to set for the points_issued property.
    */
    public function setPointsIssued(?int $value): void {
        $this->points_issued = $value;
    }

    /**
     * Sets the points_redeemed property value. Points spent on rewards in the period, including points customers brought along from another loyalty system.
     * @param int|null $value Value to set for the points_redeemed property.
    */
    public function setPointsRedeemed(?int $value): void {
        $this->points_redeemed = $value;
    }

    /**
     * Sets the points_redeemed_from_import property value. The part of points_redeemed that came from points customers brought along from another loyalty system.
     * @param int|null $value Value to set for the points_redeemed_from_import property.
    */
    public function setPointsRedeemedFromImport(?int $value): void {
        $this->points_redeemed_from_import = $value;
    }

    /**
     * Sets the redemption_rate property value. points_redeemed divided by points_issued, 3 decimals. Counts imported points when they are spent but not when they arrive, so it can pass 1.0 after an import. Use redemption_rate_excluding_import.
     * @param float|null $value Value to set for the redemption_rate property.
    */
    public function setRedemptionRate(?float $value): void {
        $this->redemption_rate = $value;
    }

    /**
     * Sets the redemption_rate_excluding_import property value. Points redeemed minus points_redeemed_from_import, divided by points_issued, 3 decimals; null when nothing was issued. The rate the dashboard shows.
     * @param float|null $value Value to set for the redemption_rate_excluding_import property.
    */
    public function setRedemptionRateExcludingImport(?float $value): void {
        $this->redemption_rate_excluding_import = $value;
    }

}
