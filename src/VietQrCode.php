<?php

namespace Takashato\VietQr;

use Takashato\VietQr\Data\AdditionalInfo;
use Takashato\VietQr\Data\MerchantInfo;
use Takashato\VietQr\Enums\Currency;
use Takashato\VietQr\Enums\InitializationMethod;
use Takashato\VietQr\Enums\Service;
use Takashato\VietQr\Enums\VietQrId;
use Takashato\VietQr\Utils\Crc16;
use Takashato\VietQr\Utils\StringUtil;

class VietQrCode
{
    protected array $data = [];

    public function __construct()
    {
        $this
            ->formatIndicator()
            ->nation('VN')
            ->currency(Currency::VND);
    }

    public function setData(VietQrId $id, $data): static
    {
        $this->data[$id->value] = $data;
        return $this;
    }

    /**
     * Data version of VietQR code. Default: 01
     * @param string $dataVersion
     * @return $this
     */
    public function formatIndicator(string $dataVersion = '01'): static
    {
        return $this->setData(VietQrId::FORMAT_INDICATOR, $dataVersion);
    }

    /**
     * Dynamic (one time) or Static (reusable)
     * @param InitializationMethod $method
     * @return $this
     */
    public function initiationMethod(InitializationMethod $method): static
    {
        return $this->setData(VietQrId::INITIATION_METHOD, $method);
    }

    /**
     * Currency of amount
     * @param Currency $currency
     * @return $this
     */
    public function currency(Currency $currency): static
    {
        return $this->setData(VietQrId::TRANSACTION_CURRENCY, $currency);
    }

    /**
     * Set merchant info object
     * @param MerchantInfo $merchant
     * @return $this
     */
    public function merchantObject(MerchantInfo $merchant): static
    {
        return $this->setData(VietQrId::MERCHANT_ACCOUNT_INFORMATION, $merchant);
    }

    /**
     * Create merchant info object, and set it
     * @param string $acquirerId
     * @param string $merchantId
     * @param Service $service
     * @return $this
     */
    public function merchant(...$args): static
    {
        return $this->setData(VietQrId::MERCHANT_ACCOUNT_INFORMATION, new MerchantInfo(...$args));
    }

    public function additionalInfoObject(AdditionalInfo $info): static
    {
        return $this->setData(VietQrId::ADDITIONAL_INFO, $info);
    }

    public function additionalInfo(...$args): static
    {
        return $this->setData(VietQrId::ADDITIONAL_INFO, new AdditionalInfo(...$args));
    }

    /**
     * Amount of the transaction
     * @param float $amount
     * @return $this
     */
    public function amount(float $amount): static
    {
        return $this->setData(VietQrId::TRANSACTION_AMOUNT, strval($amount));
    }

    /**
     * Set nation
     * @param string $nation nation code. Ex: VN
     * @return $this
     */
    public function nation(string $nation): static
    {
        return $this->setData(VietQrId::NATION, $nation);
    }

    /**
     * Build the final string
     * @return string
     */
    public function build(): string
    {
        $result = '';

        ksort($this->data);

        foreach ($this->data as $id => $data) {
            if (is_object($data) && enum_exists($data::class)) {
                $result .= StringUtil::buildWithLength($id, $data->value);
                continue;
            }

            if (is_object($data) && method_exists($data, 'build')) {
                $result .= StringUtil::buildWithLength($id, $data->build());
                continue;
            }

            $result .= StringUtil::buildWithLength($id, $data);
        }

        $result .= '6304'; // CRC and its length

        $crc = Crc16::calcAsHex($result);

        return $result . $crc;
    }
}
