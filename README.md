# Laravel VietQR

[![Latest Version on Packagist](https://img.shields.io/packagist/v/takashato/laravel-vietqr.svg?style=flat-square)](https://packagist.org/packages/takashato/laravel-vietqr)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/takashato/laravel-vietqr/run-tests.yml?branch=main&label=tests)](https://github.com/takashato/laravel-vietqr/actions?query=workflow%3Arun-tests+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/takashato/laravel-vietqr.svg?style=flat-square)](https://packagist.org/packages/takashato/laravel-vietqr)

Generate VietQR codes for Vietnamese bank transfers. Follows [NAPAS VietQR v1.5.2](https://vietqr.net/) specification (EMVCo standard).

## Features

- Static QR codes (reusable, no fixed amount)
- Dynamic QR codes (one-time use, with fixed amount)
- Bank account transfers (IBFT to account)
- Card transfers (IBFT to card)
- 50+ Vietnamese banks with BIN codes
- SVG, PNG, Base64 QR image generation
- Fully tested against official NAPAS examples

## Requirements

- PHP 8.2+
- Laravel 10, 11, or 12

## Installation

```bash
composer require takashato/laravel-vietqr
```

The package auto-registers its service provider.

## Quick Start

### Simple Bank Account QR (Static)

```php
use Takashato\VietQr\VietQrCode;
use Takashato\VietQr\Enums\Bank;

// Static QR - customer enters amount
$qr = VietQrCode::bankAccount(Bank::VIETCOMBANK, '0011012345678');

// Get QR string (for your own rendering)
$qrString = $qr->build();

// Generate SVG
$svg = $qr->generateQr('svg', 300);

// Generate Base64 PNG
$base64 = $qr->generateQrBase64('png', 300);
```

### Dynamic QR with Amount

```php
use Takashato\VietQr\VietQrCode;
use Takashato\VietQr\Enums\Bank;

// Dynamic QR - fixed amount of 180,000 VND
$qr = VietQrCode::bankAccount(
    bank: Bank::TECHCOMBANK,
    accountNumber: '19033123456789',
    amount: 180000,
    purpose: 'Thanh toan don hang #12345'
);

echo $qr->build();
```

### Card Transfer QR

```php
use Takashato\VietQr\VietQrCode;
use Takashato\VietQr\Enums\Bank;

$qr = VietQrCode::bankCard(
    bank: Bank::MB_BANK,
    cardNumber: '9704229012345678',
    amount: 50000
);
```

## Advanced Usage

### Fluent Builder API

```php
use Takashato\VietQr\VietQrCode;
use Takashato\VietQr\Data\MerchantInfo;
use Takashato\VietQr\Data\AdditionalInfo;
use Takashato\VietQr\Enums\Service;

$qr = (new VietQrCode())
    ->dynamicMethod()
    ->withMerchant(function (MerchantInfo $merchant) {
        $merchant
            ->setService(Service::NAPAS_BY_ACCOUNT)
            ->setAcquirerId('970436')  // Vietcombank BIN
            ->setMerchantId('0011012345678');
    })
    ->amount(180000)
    ->withAdditionalInfo(function (AdditionalInfo $info) {
        $info
            ->terminalLabel('POS001')
            ->purpose('Thanh toan don hang');
    })
    ->build();
```

### Using Bank Enum

```php
use Takashato\VietQr\Enums\Bank;

// Get bank BIN code
$bin = Bank::VIETCOMBANK->value; // '970436'

// Get bank names
$shortName = Bank::VIETCOMBANK->shortName(); // 'Vietcombank'
$fullName = Bank::VIETCOMBANK->nameVi(); // 'Ngân hàng TMCP Ngoại thương Việt Nam'

// Find bank by BIN
$bank = Bank::fromBin('970436'); // Bank::VIETCOMBANK

// Search banks
$results = Bank::search('viet'); // Returns matching Bank enums
```

### Using Facade

```php
use Takashato\VietQr\Facades\VietQr;

$qr = VietQr::create()
    ->staticMethod()
    ->withMerchant(...)
    ->build();
```

## Available Banks

The `Bank` enum includes 50+ Vietnamese banks:

**State-owned:** Vietcombank, VietinBank, BIDV, Agribank

**Joint-stock:** Techcombank, MB Bank, ACB, VPBank, Sacombank, TPBank, HDBank, VIB, SHB, SCB, OCB, MSB, Eximbank, SeABank, LienVietPostBank, ABBank, and more...

**Foreign branches:** HSBC, Standard Chartered, Shinhan Bank, UOB, Woori Bank, CIMB, and more...

**E-wallets:** VNPT Money, CAKE, Ubank, Timo, TNEX

## QR Code Types

| Type | Method | Amount | Use Case |
|------|--------|--------|----------|
| Static | `staticMethod()` | Customer enters | Store display, reusable |
| Dynamic | `dynamicMethod()` | Fixed in QR | Invoice, one-time payment |

## Services

| Service | Enum | Description |
|---------|------|-------------|
| NAPAS_BY_ACCOUNT | `Service::NAPAS_BY_ACCOUNT` | Transfer to bank account |
| NAPAS_BY_CARD | `Service::NAPAS_BY_CARD` | Transfer to bank card |
| QR_CASH | `Service::QR_CASH` | ATM cash withdrawal |
| QR_PUSH | `Service::QR_PUSH` | Product payment |

## Blade Usage

```blade
{{-- SVG directly in HTML --}}
{!! VietQrCode::bankAccount(Bank::BIDV, '12345678901')->generateQr('svg', 250) !!}

{{-- Base64 image --}}
<img src="data:image/png;base64,{{ VietQrCode::bankAccount(Bank::BIDV, '12345678901')->generateQrBase64('png', 250) }}" alt="VietQR">
```

## Testing

```bash
composer test
```

## Security

For security vulnerabilities, please email takashato@gmail.com.

## Credits

- [takashato](https://github.com/takashato)
- [NAPAS VietQR Specification](https://vietqr.net/)

## License

MIT License. See [LICENSE.md](LICENSE.md).