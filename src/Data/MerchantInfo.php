<?php

namespace Takashato\VietQr\Data;

use Takashato\VietQr\Enums\Service;
use Takashato\VietQr\Exceptions\MerchantInfoException;
use Takashato\VietQr\Utils\StringUtil;

class MerchantInfo
{
    public function __construct(
        protected $acquirerId = null,
        protected $merchantId = null,
        protected ?Service $service = null,
    ) {}

    public function setAcquirerId($acquirerId): self
    {
        $this->acquirerId = $acquirerId;

        return $this;
    }

    public function setMerchantId($merchantId): self
    {
        $this->merchantId = $merchantId;

        return $this;
    }

    public function setService(Service $service): self
    {
        $this->service = $service;
        return $this;
    }

    /**
     * @throws MerchantInfoException
     */
    public function build(): string
    {
        if (is_null($this->acquirerId) || is_null($this->merchantId) || is_null($this->service)) {
            throw new MerchantInfoException('Acquirer ID, Merchant ID and Service are required');
        }

        return StringUtil::buildWithLength('00', 'A000000727')
            . StringUtil::buildWithLength(
                '01',
                StringUtil::buildWithLength('00', $this->acquirerId)
                . StringUtil::buildWithLength('01', $this->merchantId),
            )
            . StringUtil::buildWithLength('02', $this->service->value);
    }
}
