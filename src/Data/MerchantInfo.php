<?php

namespace Takashato\VietQr\Data;

use Takashato\VietQr\Enums\Service;
use Takashato\VietQr\Utils\StringUtil;

class MerchantInfo
{
    public function __construct(
        protected         $acquirerId,
        protected         $merchantId,
        protected Service $service
    ) {
    }

    public function build(): string
    {
        return StringUtil::buildWithLength('00', 'A000000727')
            .StringUtil::buildWithLength('01',
                StringUtil::buildWithLength('00', $this->acquirerId)
                .StringUtil::buildWithLength('01', $this->merchantId)
            )
            .StringUtil::buildWithLength('02', $this->service->value);
    }
}
