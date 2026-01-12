<?php

namespace Takashato\VietQr;

use Closure;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Takashato\VietQr\Data\AdditionalInfo;
use Takashato\VietQr\Data\MerchantInfo;
use Takashato\VietQr\Enums\Bank;
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
            ->countryCode('VN')
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
            ->withMerchant(fn(MerchantInfo $m) => $m->forAccountTransfer($bank, $accountNumber));

        if ($amount !== null) {
            $qr->dynamicMethod()->amount($amount);
        } else {
            $qr->staticMethod();
        }

        if ($purpose !== null) {
            $qr->withAdditionalInfo(fn(AdditionalInfo $a) => $a->purpose($purpose));
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
            ->withMerchant(fn(MerchantInfo $m) => $m->forCardTransfer($bank, $cardNumber));

        if ($amount !== null) {
            $qr->dynamicMethod()->amount($amount);
        } else {
            $qr->staticMethod();
        }

        if ($purpose !== null) {
            $qr->withAdditionalInfo(fn(AdditionalInfo $a) => $a->purpose($purpose));
        }

        return $qr;
    }

    /**
     * Quick QR for merchant payment (QR PUSH).
     *
     * @param  Bank|string  $bank  Bank enum or BIN code
     * @param  string  $merchantId  Merchant account number
     * @param  string  $merchantName  Merchant name (max 25 chars)
     * @param  string  $merchantCity  Merchant city (max 15 chars)
     * @param  string  $mcc  Merchant Category Code (4 digits)
     * @param  float|null  $amount  Optional: fixed amount (makes it dynamic)
     * @param  string|null  $purpose  Optional: payment purpose/description
     */
    public static function merchantPayment(
        Bank|string $bank,
        string $merchantId,
        string $merchantName,
        string $merchantCity,
        string $mcc = '5999',
        ?float $amount = null,
        ?string $purpose = null,
    ): self {
        $qr = (new self())
            ->withMerchant(fn(MerchantInfo $m) => $m
                ->setAcquirerId($bank)
                ->setMerchantId($merchantId)
                ->setService(Service::QR_PUSH))
            ->merchantName($merchantName)
            ->merchantCity($merchantCity)
            ->merchantCategoryCode($mcc);

        if ($amount !== null) {
            $qr->dynamicMethod()->amount($amount);
        } else {
            $qr->staticMethod();
        }

        if ($purpose !== null) {
            $qr->withAdditionalInfo(fn(AdditionalInfo $a) => $a->purpose($purpose));
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
     * Set country code (ISO 3166-1 alpha-2).
     *
     * @param string $countryCode Country code (e.g., "VN")
     * @return $this
     */
    public function countryCode(string $countryCode): self
    {
        return $this->setData(VietQrId::COUNTRY_CODE, $countryCode);
    }

    /**
     * Set country code.
     *
     * @deprecated Use countryCode() instead
     * @param string $nation Country code. Ex: VN
     * @return $this
     */
    public function nation(string $nation): self
    {
        return $this->countryCode($nation);
    }

    /**
     * Set merchant name (required for QR PUSH).
     *
     * @param string $merchantName Merchant name (max 25 chars)
     * @return $this
     */
    public function merchantName(string $merchantName): self
    {
        return $this->setData(VietQrId::MERCHANT_NAME, $merchantName);
    }

    /**
     * Set merchant city (required for QR PUSH).
     *
     * @param string $merchantCity Merchant city (max 15 chars)
     * @return $this
     */
    public function merchantCity(string $merchantCity): self
    {
        return $this->setData(VietQrId::MERCHANT_CITY, $merchantCity);
    }

    /**
     * Set postal code.
     *
     * @param string $postalCode Postal code (max 10 chars)
     * @return $this
     */
    public function postalCode(string $postalCode): self
    {
        return $this->setData(VietQrId::POSTAL_CODE, $postalCode);
    }

    /**
     * Set merchant category code (MCC).
     *
     * @param string $mcc 4-digit MCC (e.g., "5812" for restaurants)
     * @return $this
     */
    public function merchantCategoryCode(string $mcc): self
    {
        return $this->setData(VietQrId::MERCHANT_CATEGORY_CODE, $mcc);
    }

    /**
     * Alias for merchantCategoryCode().
     */
    public function mcc(string $mcc): self
    {
        return $this->merchantCategoryCode($mcc);
    }

    /**
     * Set tip or convenience indicator.
     *
     * Values:
     * - "01": Tip prompted (user enters tip)
     * - "02": Convenience fee fixed (use convenienceFeeFixed())
     * - "03": Convenience fee percentage (use convenienceFeePercentage())
     *
     * @param string $indicator Indicator value ("01", "02", or "03")
     * @return $this
     */
    public function tipIndicator(string $indicator): self
    {
        return $this->setData(VietQrId::TIP_OR_CONVENIENCE_INDICATOR, $indicator);
    }

    /**
     * Set fixed convenience fee amount.
     * Automatically sets tip indicator to "02".
     *
     * @param float $amount Fixed fee amount
     * @return $this
     */
    public function convenienceFeeFixed(float $amount): self
    {
        $this->tipIndicator('02');

        return $this->setData(VietQrId::CONVENIENCE_FEE_FIXED, strval($amount));
    }

    /**
     * Set convenience fee as percentage.
     * Automatically sets tip indicator to "03".
     *
     * @param float $percentage Fee percentage (e.g., 5.5 for 5.5%)
     * @return $this
     */
    public function convenienceFeePercentage(float $percentage): self
    {
        $this->tipIndicator('03');

        return $this->setData(VietQrId::CONVENIENCE_FEE_PERCENTAGE, strval($percentage));
    }

    /**
     * Enable tip prompting (user enters tip amount).
     *
     * @return $this
     */
    public function promptTip(): self
    {
        return $this->tipIndicator('01');
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
