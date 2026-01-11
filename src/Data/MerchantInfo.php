<?php

namespace Takashato\VietQr\Data;

use Takashato\VietQr\Enums\Bank;
use Takashato\VietQr\Enums\Service;
use Takashato\VietQr\Exceptions\MerchantInfoException;
use Takashato\VietQr\Utils\StringUtil;

class MerchantInfo
{
    public function __construct(
        protected Bank|string|null $acquirerId = null,
        protected ?string $merchantId = null,
        protected ?Service $service = null,
    ) {}

    /**
     * Set the acquirer (bank) ID using BIN code or Bank enum.
     */
    public function setAcquirerId(Bank|string $acquirerId): self
    {
        $this->acquirerId = $acquirerId;

        return $this;
    }

    /**
     * Set the bank using Bank enum (alias for setAcquirerId).
     */
    public function setBank(Bank $bank): self
    {
        return $this->setAcquirerId($bank);
    }

    /**
     * Set the merchant ID (account number or card number).
     */
    public function setMerchantId(string $merchantId): self
    {
        $this->merchantId = $merchantId;

        return $this;
    }

    /**
     * Set the account number (alias for setMerchantId).
     */
    public function setAccountNumber(string $accountNumber): self
    {
        return $this->setMerchantId($accountNumber);
    }

    /**
     * Set the card number (alias for setMerchantId).
     */
    public function setCardNumber(string $cardNumber): self
    {
        return $this->setMerchantId($cardNumber);
    }

    public function setService(Service $service): self
    {
        $this->service = $service;

        return $this;
    }

    /**
     * Convenience method: Set up for bank transfer to account.
     */
    public function forAccountTransfer(Bank|string $bank, string $accountNumber): self
    {
        return $this
            ->setAcquirerId($bank)
            ->setMerchantId($accountNumber)
            ->setService(Service::NAPAS_BY_ACCOUNT);
    }

    /**
     * Convenience method: Set up for bank transfer to card.
     */
    public function forCardTransfer(Bank|string $bank, string $cardNumber): self
    {
        return $this
            ->setAcquirerId($bank)
            ->setMerchantId($cardNumber)
            ->setService(Service::NAPAS_BY_CARD);
    }

    /**
     * Get the resolved acquirer ID (BIN code).
     */
    protected function resolveAcquirerId(): ?string
    {
        if ($this->acquirerId instanceof Bank) {
            return $this->acquirerId->value;
        }

        return $this->acquirerId;
    }

    /**
     * @throws MerchantInfoException
     */
    public function build(): string
    {
        $acquirerId = $this->resolveAcquirerId();

        if (is_null($acquirerId) || is_null($this->merchantId) || is_null($this->service)) {
            throw new MerchantInfoException('Acquirer ID, Merchant ID and Service are required');
        }

        return StringUtil::buildWithLength('00', 'A000000727')
            . StringUtil::buildWithLength(
                '01',
                StringUtil::buildWithLength('00', $acquirerId)
                . StringUtil::buildWithLength('01', $this->merchantId),
            )
            . StringUtil::buildWithLength('02', $this->service->value);
    }
}
