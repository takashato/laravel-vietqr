<?php

namespace Takashato\VietQr;

use Closure;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Takashato\VietQr\Data\AdditionalInfo;
use Takashato\VietQr\Data\MerchantInfo;
use Takashato\VietQr\Enums\Bank;
use Takashato\VietQr\Enums\Currency;
use Takashato\VietQr\Enums\InitializationMethod;
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

    /**
     * Quick static QR for bank account transfer.
     *
     * @param  Bank|string  $bank  Bank enum or BIN code
     * @param  string  $accountNumber  Account number
     * @param  float|null  $amount  Optional: fixed amount (makes it dynamic)
     * @param  string|null  $purpose  Optional: payment purpose/description
     */
    public static function bankAccount(
        Bank|string $bank,
        string $accountNumber,
        ?float $amount = null,
        ?string $purpose = null,
    ): self {
        $qr = (new self())
            ->withMerchant(fn (MerchantInfo $m) => $m->forAccountTransfer($bank, $accountNumber));

        if ($amount !== null) {
            $qr->dynamicMethod()->amount($amount);
        } else {
            $qr->staticMethod();
        }

        if ($purpose !== null) {
            $qr->withAdditionalInfo(fn (AdditionalInfo $a) => $a->purpose($purpose));
        }

        return $qr;
    }

    /**
     * Quick static QR for card transfer.
     *
     * @param  Bank|string  $bank  Bank enum or BIN code
     * @param  string  $cardNumber  Card number
     * @param  float|null  $amount  Optional: fixed amount (makes it dynamic)
     * @param  string|null  $purpose  Optional: payment purpose/description
     */
    public static function bankCard(
        Bank|string $bank,
        string $cardNumber,
        ?float $amount = null,
        ?string $purpose = null,
    ): self {
        $qr = (new self())
            ->withMerchant(fn (MerchantInfo $m) => $m->forCardTransfer($bank, $cardNumber));

        if ($amount !== null) {
            $qr->dynamicMethod()->amount($amount);
        } else {
            $qr->staticMethod();
        }

        if ($purpose !== null) {
            $qr->withAdditionalInfo(fn (AdditionalInfo $a) => $a->purpose($purpose));
        }

        return $qr;
    }

    public function setData(VietQrId $id, $data): self
    {
        $this->data[$id->value] = $data;

        return $this;
    }

    /**
     * Data version of VietQR code. Default: 01
     *
     * @param string $dataVersion
     * @return $this
     */
    public function formatIndicator(string $dataVersion = '01'): self
    {
        return $this->setData(VietQrId::FORMAT_INDICATOR, $dataVersion);
    }

    /**
     * Dynamic (one time) or Static (reusable)
     *
     * @param InitializationMethod $method
     * @return $this
     */
    public function initiationMethod(InitializationMethod $method): self
    {
        return $this->setData(VietQrId::INITIATION_METHOD, $method);
    }

    public function dynamicMethod()
    {
        return $this->initiationMethod(InitializationMethod::DYNAMIC);
    }

    public function staticMethod()
    {
        return $this->initiationMethod(InitializationMethod::STATIC);
    }

    /**
     * Currency of amount
     *
     * @param Currency $currency
     * @return $this
     */
    public function currency(Currency $currency): self
    {
        return $this->setData(VietQrId::TRANSACTION_CURRENCY, $currency);
    }

    /**
     * Set merchant info object
     *
     * @param MerchantInfo $merchant
     * @return $this
     */
    public function merchantObject(MerchantInfo $merchant): self
    {
        return $this->setData(VietQrId::MERCHANT_ACCOUNT_INFORMATION, $merchant);
    }

    public function withMerchant(Closure|MerchantInfo $closure)
    {
        if ($closure instanceof Closure) {
            $merchantInfo = tap(new MerchantInfo(), $closure);

            return $this->merchantObject($merchantInfo);
        }

        return $this->merchantObject($closure);
    }

    /**
     * Create merchant info object, and set it
     *
     * @param mixed ...$args
     * @return $this
     */
    public function merchant(...$args): self
    {
        return $this->setData(VietQrId::MERCHANT_ACCOUNT_INFORMATION, new MerchantInfo(...$args));
    }

    public function additionalInfoObject(AdditionalInfo $info): self
    {
        return $this->setData(VietQrId::ADDITIONAL_INFO, $info);
    }

    public function additionalInfo(...$args): self
    {
        return $this->setData(VietQrId::ADDITIONAL_INFO, new AdditionalInfo(...$args));
    }

    public function withAdditionalInfo(Closure|AdditionalInfo $closure): self
    {
        if ($closure instanceof Closure) {
            $additionalInfo = tap(new AdditionalInfo(), $closure);

            return $this->additionalInfoObject($additionalInfo);
        }

        return $this->additionalInfoObject($closure);
    }

    /**
     * Amount of the transaction
     *
     * @param float $amount
     * @return $this
     */
    public function amount(float $amount): self
    {
        return $this->setData(VietQrId::TRANSACTION_AMOUNT, strval($amount));
    }

    /**
     * Set nation
     *
     * @param string $nation nation code. Ex: VN
     * @return $this
     */
    public function nation(string $nation): self
    {
        return $this->setData(VietQrId::NATION, $nation);
    }

    /**
     * Build the final string
     *
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

    public function generateQr(string $format = 'svg', int $size = 200)
    {
        return QrCode::format($format)
            ->errorCorrection('h')
            ->size($size)
            ->generate($this->build());
    }

    public function generateQrBase64(...$args): string
    {
        return base64_encode($this->generateQr(...$args));
    }
}
