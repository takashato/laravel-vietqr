# Changelog

All notable changes to `laravel-vietqr` will be documented in this file.

## [Unreleased]

### Added
- **Laravel 10, 11, 12 support** - Updated dependencies for latest Laravel versions
- **Bank enum** - 50+ Vietnamese banks with BIN codes, Vietnamese names, and short names
  - `Bank::VIETCOMBANK`, `Bank::BIDV`, `Bank::TECHCOMBANK`, etc.
  - `$bank->nameVi()` - Full Vietnamese name
  - `$bank->shortName()` - Common short name
  - `Bank::fromBin('970436')` - Lookup by BIN code
  - `Bank::search('viet')` - Search banks by name
- **Static helper methods** for quick QR generation
  - `VietQrCode::bankAccount(Bank $bank, string $account, ?float $amount, ?string $purpose)`
  - `VietQrCode::bankCard(Bank $bank, string $card, ?float $amount, ?string $purpose)`
- **MerchantInfo convenience methods**
  - `setBank(Bank $bank)` - Use Bank enum directly
  - `setAccountNumber(string $account)` - Alias for setMerchantId
  - `forAccountTransfer(Bank $bank, string $account)` - Quick setup for NAPAS_BY_ACCOUNT
  - `forCardTransfer(Bank $bank, string $card)` - Quick setup for NAPAS_BY_CARD
- **Comprehensive tests** for Bank enum, static helpers, and NAPAS format validation

### Changed
- PHP requirement bumped to 8.2+
- Updated `spatie/laravel-package-tools` to ^1.16
- Updated `pestphp/pest` to ^2.0|^3.0
- Updated `larastan/larastan` to ^2.0|^3.0 (renamed from nunomaduro/larastan)

### Removed
- Laravel 9 support (EOL)

## [1.0.0] - 2022-12-21

### Added
- Initial release
- VietQR code generation following NAPAS v1.5.2 specification
- Static and dynamic QR code support
- Bank transfer to account (QRIBFTTA)
- Bank transfer to card (QRIBFTTC)
- QR code image generation via simplesoftwareio/simple-qrcode
- CRC16 checksum calculation
- Laravel Facade support