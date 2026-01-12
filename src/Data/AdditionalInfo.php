<?php

namespace Takashato\VietQr\Data;

use Takashato\VietQr\Enums\AdditionalInfoId;
use Takashato\VietQr\Utils\StringUtil;

class AdditionalInfo
{
    public function __construct(
        protected ?string $billNumber = null,
        protected ?string $mobileNumber = null,
        protected ?string $storeLabel = null,
        protected ?string $loyaltyNumber = null,
        protected ?string $referenceLabel = null,
        protected ?string $customerLabel = null,
        protected ?string $terminalLabel = null,
        protected ?string $purpose = null,
        protected ?string $consumerDataRequest = null,
    ) {}

    /**
     * Bill/invoice number (ID 01).
     */
    public function billNumber(string $billNumber): self
    {
        $this->billNumber = $billNumber;

        return $this;
    }

    /**
     * Mobile/phone number (ID 02).
     */
    public function mobileNumber(string $mobileNumber): self
    {
        $this->mobileNumber = $mobileNumber;

        return $this;
    }

    /**
     * Store identifier (ID 03).
     */
    public function storeLabel(string $storeLabel): self
    {
        $this->storeLabel = $storeLabel;

        return $this;
    }

    /**
     * Loyalty card number (ID 04).
     */
    public function loyaltyNumber(string $loyaltyNumber): self
    {
        $this->loyaltyNumber = $loyaltyNumber;

        return $this;
    }

    /**
     * Transaction reference (ID 05).
     */
    public function referenceLabel(string $referenceLabel): self
    {
        $this->referenceLabel = $referenceLabel;

        return $this;
    }

    /**
     * Customer identifier (ID 06).
     */
    public function customerLabel(string $customerLabel): self
    {
        $this->customerLabel = $customerLabel;

        return $this;
    }

    /**
     * POS terminal identifier (ID 07).
     */
    public function terminalLabel(string $terminalLabel): self
    {
        $this->terminalLabel = $terminalLabel;

        return $this;
    }

    /**
     * Payment description/purpose (ID 08).
     */
    public function purpose(string $purpose): self
    {
        $this->purpose = $purpose;

        return $this;
    }

    /**
     * Request additional consumer data (ID 09).
     * Use: A=Address, M=Mobile, E=Email (can combine, e.g., "AME").
     */
    public function consumerDataRequest(string $consumerDataRequest): self
    {
        $this->consumerDataRequest = $consumerDataRequest;

        return $this;
    }

    public function build(): string
    {
        $result = '';

        if ($this->billNumber !== null) {
            $result .= StringUtil::buildWithLength(AdditionalInfoId::BILL_NUMBER->value, $this->billNumber);
        }
        if ($this->mobileNumber !== null) {
            $result .= StringUtil::buildWithLength(AdditionalInfoId::MOBILE_NUMBER->value, $this->mobileNumber);
        }
        if ($this->storeLabel !== null) {
            $result .= StringUtil::buildWithLength(AdditionalInfoId::STORE_LABEL->value, $this->storeLabel);
        }
        if ($this->loyaltyNumber !== null) {
            $result .= StringUtil::buildWithLength(AdditionalInfoId::LOYALTY_NUMBER->value, $this->loyaltyNumber);
        }
        if ($this->referenceLabel !== null) {
            $result .= StringUtil::buildWithLength(AdditionalInfoId::REFERENCE_LABEL->value, $this->referenceLabel);
        }
        if ($this->customerLabel !== null) {
            $result .= StringUtil::buildWithLength(AdditionalInfoId::CUSTOMER_LABEL->value, $this->customerLabel);
        }
        if ($this->terminalLabel !== null) {
            $result .= StringUtil::buildWithLength(AdditionalInfoId::TERMINAL_LABEL->value, $this->terminalLabel);
        }
        if ($this->purpose !== null) {
            $result .= StringUtil::buildWithLength(AdditionalInfoId::PURPOSE->value, $this->purpose);
        }
        if ($this->consumerDataRequest !== null) {
            $result .= StringUtil::buildWithLength(AdditionalInfoId::CONSUMER_DATA_REQUEST->value, $this->consumerDataRequest);
        }

        return $result;
    }
}
