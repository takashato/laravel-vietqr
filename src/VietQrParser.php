<?php

namespace Takashato\VietQr;

use Takashato\VietQr\Exceptions\InvalidQrException;
use Takashato\VietQr\Utils\Crc16;

class VietQrParser
{
    public const NAPAS_GUID = 'A000000727';

    protected string $raw;

    protected array $data = [];

    protected array $errors = [];

    public function __construct(string $qrString)
    {
        $this->raw = $qrString;
        $this->data = $this->parseTlv($qrString);
    }

    /**
     * Parse a VietQR string.
     */
    public static function parse(string $qrString): self
    {
        return new self($qrString);
    }

    /**
     * Validate the QR string and return true if valid.
     */
    public function isValid(): bool
    {
        $this->errors = [];

        // Check format indicator
        if (! isset($this->data['00']) || $this->data['00'] !== '01') {
            $this->errors[] = 'Missing or invalid format indicator (ID 00)';
        }

        // Check CRC
        if (! $this->validateCrc()) {
            $this->errors[] = 'Invalid CRC checksum';
        }

        // Check merchant account info exists
        if (! isset($this->data['38'])) {
            $this->errors[] = 'Missing merchant account information (ID 38)';
        } else {
            $this->validateMerchantInfo();
        }

        // Check currency
        if (! isset($this->data['53'])) {
            $this->errors[] = 'Missing transaction currency (ID 53)';
        }

        // Check country code
        if (! isset($this->data['58'])) {
            $this->errors[] = 'Missing country code (ID 58)';
        }

        return empty($this->errors);
    }

    /**
     * Validate and throw exception if invalid.
     *
     * @throws InvalidQrException
     */
    public function validate(): self
    {
        if (! $this->isValid()) {
            throw new InvalidQrException(implode('; ', $this->errors));
        }

        return $this;
    }

    /**
     * Get validation errors.
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Get all parsed data.
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * Get a specific field by ID.
     */
    public function get(string $id): ?string
    {
        return $this->data[$id] ?? null;
    }

    /**
     * Get the raw QR string.
     */
    public function getRaw(): string
    {
        return $this->raw;
    }

    /**
     * Get initiation method (11=static, 12=dynamic).
     */
    public function getInitiationMethod(): ?string
    {
        return $this->data['01'] ?? null;
    }

    /**
     * Check if this is a static QR.
     */
    public function isStatic(): bool
    {
        return $this->getInitiationMethod() === '11';
    }

    /**
     * Check if this is a dynamic QR.
     */
    public function isDynamic(): bool
    {
        return $this->getInitiationMethod() === '12';
    }

    /**
     * Get transaction amount.
     */
    public function getAmount(): ?float
    {
        $amount = $this->data['54'] ?? null;

        return $amount !== null ? (float) $amount : null;
    }

    /**
     * Get currency code.
     */
    public function getCurrency(): ?string
    {
        return $this->data['53'] ?? null;
    }

    /**
     * Get country code.
     */
    public function getCountryCode(): ?string
    {
        return $this->data['58'] ?? null;
    }

    /**
     * Get merchant name.
     */
    public function getMerchantName(): ?string
    {
        return $this->data['59'] ?? null;
    }

    /**
     * Get merchant city.
     */
    public function getMerchantCity(): ?string
    {
        return $this->data['60'] ?? null;
    }

    /**
     * Get merchant category code.
     */
    public function getMerchantCategoryCode(): ?string
    {
        return $this->data['52'] ?? null;
    }

    /**
     * Get parsed merchant account info (ID 38).
     */
    public function getMerchantAccountInfo(): ?array
    {
        if (! isset($this->data['38'])) {
            return null;
        }

        $parsed = $this->parseTlv($this->data['38']);

        // Parse nested payment network data (sub-tag 01)
        if (isset($parsed['01'])) {
            $parsed['01_parsed'] = $this->parseTlv($parsed['01']);
        }

        return $parsed;
    }

    /**
     * Get bank BIN code from merchant info.
     */
    public function getBankBin(): ?string
    {
        $merchantInfo = $this->getMerchantAccountInfo();

        return $merchantInfo['01_parsed']['00'] ?? null;
    }

    /**
     * Get account/card number from merchant info.
     */
    public function getAccountNumber(): ?string
    {
        $merchantInfo = $this->getMerchantAccountInfo();

        return $merchantInfo['01_parsed']['01'] ?? null;
    }

    /**
     * Get service code from merchant info.
     */
    public function getServiceCode(): ?string
    {
        $merchantInfo = $this->getMerchantAccountInfo();

        return $merchantInfo['02'] ?? null;
    }

    /**
     * Check if this is a NAPAS QR code.
     */
    public function isNapasQr(): bool
    {
        $merchantInfo = $this->getMerchantAccountInfo();

        return isset($merchantInfo['00']) && $merchantInfo['00'] === self::NAPAS_GUID;
    }

    /**
     * Get parsed additional data (ID 62).
     */
    public function getAdditionalData(): ?array
    {
        if (! isset($this->data['62'])) {
            return null;
        }

        return $this->parseTlv($this->data['62']);
    }

    /**
     * Get purpose of transaction from additional data.
     */
    public function getPurpose(): ?string
    {
        $additionalData = $this->getAdditionalData();

        return $additionalData['08'] ?? null;
    }

    /**
     * Get terminal label from additional data.
     */
    public function getTerminalLabel(): ?string
    {
        $additionalData = $this->getAdditionalData();

        return $additionalData['07'] ?? null;
    }

    /**
     * Get bill number from additional data.
     */
    public function getBillNumber(): ?string
    {
        $additionalData = $this->getAdditionalData();

        return $additionalData['01'] ?? null;
    }

    /**
     * Get reference label from additional data.
     */
    public function getReferenceLabel(): ?string
    {
        $additionalData = $this->getAdditionalData();

        return $additionalData['05'] ?? null;
    }

    /**
     * Get CRC from the QR string.
     */
    public function getCrc(): ?string
    {
        return $this->data['63'] ?? null;
    }

    /**
     * Validate CRC checksum.
     */
    public function validateCrc(): bool
    {
        $crc = $this->getCrc();
        if ($crc === null) {
            return false;
        }

        // Calculate CRC on everything except the CRC value (last 4 chars)
        $dataWithoutCrc = substr($this->raw, 0, -4);
        $calculatedCrc = Crc16::calcAsHex($dataWithoutCrc);

        return strtoupper($crc) === strtoupper($calculatedCrc);
    }

    /**
     * Parse TLV encoded string into associative array.
     */
    protected function parseTlv(string $data): array
    {
        $result = [];
        $position = 0;
        $length = strlen($data);

        while ($position < $length) {
            // Need at least 4 characters for ID (2) + Length (2)
            if ($position + 4 > $length) {
                break;
            }

            $id = substr($data, $position, 2);
            $valueLength = (int) substr($data, $position + 2, 2);

            // Validate we have enough data for the value
            if ($position + 4 + $valueLength > $length) {
                break;
            }

            $value = substr($data, $position + 4, $valueLength);
            $result[$id] = $value;

            $position += 4 + $valueLength;
        }

        return $result;
    }

    /**
     * Validate merchant account information structure.
     */
    protected function validateMerchantInfo(): void
    {
        $merchantInfo = $this->getMerchantAccountInfo();

        if (! isset($merchantInfo['00'])) {
            $this->errors[] = 'Missing GUID in merchant account info (sub-tag 00)';
        } elseif ($merchantInfo['00'] !== self::NAPAS_GUID) {
            $this->errors[] = 'Invalid GUID in merchant account info, expected ' . self::NAPAS_GUID;
        }

        if (! isset($merchantInfo['01'])) {
            $this->errors[] = 'Missing payment network data in merchant account info (sub-tag 01)';
        } else {
            $paymentData = $merchantInfo['01_parsed'] ?? [];

            if (! isset($paymentData['00'])) {
                $this->errors[] = 'Missing bank BIN in merchant account info';
            }

            if (! isset($paymentData['01'])) {
                $this->errors[] = 'Missing account/card number in merchant account info';
            }
        }
    }

    /**
     * Convert to array representation.
     */
    public function toArray(): array
    {
        return [
            'raw' => $this->raw,
            'valid' => $this->isValid(),
            'errors' => $this->errors,
            'is_napas' => $this->isNapasQr(),
            'is_static' => $this->isStatic(),
            'is_dynamic' => $this->isDynamic(),
            'format_indicator' => $this->get('00'),
            'initiation_method' => $this->getInitiationMethod(),
            'bank_bin' => $this->getBankBin(),
            'account_number' => $this->getAccountNumber(),
            'service_code' => $this->getServiceCode(),
            'merchant_category_code' => $this->getMerchantCategoryCode(),
            'currency' => $this->getCurrency(),
            'amount' => $this->getAmount(),
            'country_code' => $this->getCountryCode(),
            'merchant_name' => $this->getMerchantName(),
            'merchant_city' => $this->getMerchantCity(),
            'purpose' => $this->getPurpose(),
            'terminal_label' => $this->getTerminalLabel(),
            'bill_number' => $this->getBillNumber(),
            'reference_label' => $this->getReferenceLabel(),
            'crc' => $this->getCrc(),
            'crc_valid' => $this->validateCrc(),
        ];
    }
}
