<?php

use Takashato\VietQr\Data\AdditionalInfo;
use Takashato\VietQr\Data\MerchantInfo;
use Takashato\VietQr\Enums\Bank;
use Takashato\VietQr\Enums\Service;
use Takashato\VietQr\Facades\VietQr as VietQrFacade;
use Takashato\VietQr\VietQr;
use Takashato\VietQr\VietQrCode;

it('can test', function () {
    expect(true)->toBeTrue();
});

it('registered through service container', function () {
    expect($this->app->make(VietQr::class))
        ->toBeInstanceOf(VietQr::class);
});

it('can use facade', function () {
    expect(VietQrFacade::create())
        ->toBeInstanceOf(VietQrCode::class);
});

// example in the VietQR docs: https://vietqr.net/portal-service/download/documents/QR_Format_T&C_v1.5.2_EN_102022.pdf
it('can create static IBFT to account', function () {
    expect(
        (new VietQrCode())
            ->staticMethod()
            ->withMerchant(function (MerchantInfo $merchantInfo) {
                $merchantInfo
                    ->setService(Service::NAPAS_BY_ACCOUNT)
                    ->setAcquirerId('970403')
                    ->setMerchantId('0011012345678');
            })
            ->build(),
    )
        ->toBeString()
        ->toEqual('00020101021138570010A00000072701270006970403011300110123456780208QRIBFTTA53037045802VN63049E6F');
});

it('can create static IBFT to card', function () {
    expect(
        (new VietQrCode())
            ->staticMethod()
            ->withMerchant(function (MerchantInfo $merchantInfo) {
                $merchantInfo
                    ->setService(Service::NAPAS_BY_CARD)
                    ->setAcquirerId('970403')
                    ->setMerchantId('9704031101234567');
            })
            ->build(),
    )
        ->toBeString()
        ->toEqual('00020101021138600010A00000072701300006970403011697040311012345670208QRIBFTTC53037045802VN63044F52');
});

it('can create dynamic IBFT to account', function () {
    expect(
        (new VietQrCode())
            ->dynamicMethod()
            ->withMerchant(function (MerchantInfo $merchantInfo) {
                $merchantInfo
                    ->setService(Service::NAPAS_BY_ACCOUNT)
                    ->setAcquirerId('970403')
                    ->setMerchantId('0011012345678');
            })
            ->amount(180000)
            ->withAdditionalInfo(function (AdditionalInfo $additionalInfo) {
                $additionalInfo
                    ->terminalLabel('NPS6869')
                    ->purpose('thanh toan don hang');
            })
            ->build(),
    )
        ->toBeString()
        // Note: terminalLabel uses ID 07 (spec-compliant), not 01
        ->toEqual('00020101021238570010A00000072701270006970403011300110123456780208QRIBFTTA530370454061800005802VN62340707NPS68690819thanh toan don hang630437D6');
});

it('can create dynamic IBFT to card', function () {
    expect(
        (new VietQrCode())
            ->dynamicMethod()
            ->withMerchant(function (MerchantInfo $merchantInfo) {
                $merchantInfo
                    ->setService(Service::NAPAS_BY_CARD)
                    ->setAcquirerId('970403')
                    ->setMerchantId('9704031101234567');
            })
            ->amount(180000)
            ->withAdditionalInfo(function (AdditionalInfo $additionalInfo) {
                $additionalInfo
                    ->terminalLabel('NPS6869')
                    ->purpose('thanh toan don hang');
            })
            ->build(),
    )
        ->toBeString()
        // Note: terminalLabel uses ID 07 (spec-compliant), not 01
        ->toEqual('00020101021238600010A00000072701300006970403011697040311012345670208QRIBFTTC530370454061800005802VN62340707NPS68690819thanh toan don hang6304BBFB');
});

// Bank enum tests
it('can get bank BIN code', function () {
    expect(Bank::VIETCOMBANK->value)->toBe('970436');
    expect(Bank::SACOMBANK->value)->toBe('970403');
    expect(Bank::TECHCOMBANK->value)->toBe('970407');
});

it('can get bank short name', function () {
    expect(Bank::VIETCOMBANK->shortName())->toBe('Vietcombank');
    expect(Bank::MB_BANK->shortName())->toBe('MB Bank');
    expect(Bank::BIDV->shortName())->toBe('BIDV');
});

it('can get bank Vietnamese name', function () {
    expect(Bank::VIETCOMBANK->nameVi())->toBe('Ngân hàng TMCP Ngoại thương Việt Nam');
    expect(Bank::BIDV->nameVi())->toBe('Ngân hàng TMCP Đầu tư và Phát triển Việt Nam');
});

it('can find bank from BIN', function () {
    expect(Bank::fromBin('970436'))->toBe(Bank::VIETCOMBANK);
    expect(Bank::fromBin('970403'))->toBe(Bank::SACOMBANK);
    expect(Bank::fromBin('invalid'))->toBeNull();
});

it('can search banks', function () {
    $results = Bank::search('vietcom');
    expect($results)->toContain(Bank::VIETCOMBANK);

    $results = Bank::search('mb');
    expect($results)->toContain(Bank::MB_BANK);
});

// MerchantInfo with Bank enum tests
it('can use Bank enum in MerchantInfo', function () {
    $qr = (new VietQrCode())
        ->staticMethod()
        ->withMerchant(function (MerchantInfo $merchantInfo) {
            $merchantInfo
                ->setBank(Bank::SACOMBANK)
                ->setAccountNumber('0011012345678')
                ->setService(Service::NAPAS_BY_ACCOUNT);
        })
        ->build();

    // Same as using '970403' directly (Sacombank BIN)
    expect($qr)->toEqual('00020101021138570010A00000072701270006970403011300110123456780208QRIBFTTA53037045802VN63049E6F');
});

it('can use forAccountTransfer helper', function () {
    $qr = (new VietQrCode())
        ->staticMethod()
        ->withMerchant(function (MerchantInfo $merchantInfo) {
            $merchantInfo->forAccountTransfer(Bank::SACOMBANK, '0011012345678');
        })
        ->build();

    expect($qr)->toEqual('00020101021138570010A00000072701270006970403011300110123456780208QRIBFTTA53037045802VN63049E6F');
});

it('can use forCardTransfer helper', function () {
    $qr = (new VietQrCode())
        ->staticMethod()
        ->withMerchant(function (MerchantInfo $merchantInfo) {
            $merchantInfo->forCardTransfer(Bank::SACOMBANK, '9704031101234567');
        })
        ->build();

    expect($qr)->toEqual('00020101021138600010A00000072701300006970403011697040311012345670208QRIBFTTC53037045802VN63044F52');
});

// Static helper method tests
it('can create static bank account QR using helper', function () {
    $qr = VietQrCode::bankAccount(Bank::SACOMBANK, '0011012345678')->build();
    expect($qr)->toEqual('00020101021138570010A00000072701270006970403011300110123456780208QRIBFTTA53037045802VN63049E6F');
});

it('can create dynamic bank account QR with amount using helper', function () {
    $qr = VietQrCode::bankAccount(
        bank: Bank::SACOMBANK,
        accountNumber: '0011012345678',
        amount: 180000,
        purpose: 'thanh toan don hang',
    )->build();

    // This should be dynamic with amount and purpose
    expect($qr)
        ->toContain('0102') // Dynamic method indicator
        ->toContain('540618') // Amount 180000
        ->toContain('thanh toan don hang');
});

it('can create static bank card QR using helper', function () {
    $qr = VietQrCode::bankCard(Bank::SACOMBANK, '9704031101234567')->build();
    expect($qr)->toEqual('00020101021138600010A00000072701300006970403011697040311012345670208QRIBFTTC53037045802VN63044F52');
});

it('can use BIN string instead of Bank enum', function () {
    $qr = VietQrCode::bankAccount('970403', '0011012345678')->build();
    expect($qr)->toEqual('00020101021138570010A00000072701270006970403011300110123456780208QRIBFTTA53037045802VN63049E6F');
});

// ============================================
// QR Parser Tests
// ============================================

it('can parse static IBFT QR string', function () {
    $qrString = '00020101021138570010A00000072701270006970403011300110123456780208QRIBFTTA53037045802VN63049E6F';
    $parser = \Takashato\VietQr\VietQrParser::parse($qrString);

    expect($parser->isValid())->toBeTrue();
    expect($parser->isNapasQr())->toBeTrue();
    expect($parser->isStatic())->toBeTrue();
    expect($parser->isDynamic())->toBeFalse();
    expect($parser->getBankBin())->toBe('970403');
    expect($parser->getAccountNumber())->toBe('0011012345678');
    expect($parser->getServiceCode())->toBe('QRIBFTTA');
    expect($parser->getCurrency())->toBe('704');
    expect($parser->getCountryCode())->toBe('VN');
    expect($parser->validateCrc())->toBeTrue();
});

it('can parse dynamic IBFT QR string', function () {
    $qrString = '00020101021238570010A00000072701270006970403011300110123456780208QRIBFTTA530370454061800005802VN62340707NPS68690819thanh toan don hang630437D6';
    $parser = \Takashato\VietQr\VietQrParser::parse($qrString);

    expect($parser->isValid())->toBeTrue();
    expect($parser->isDynamic())->toBeTrue();
    expect($parser->getAmount())->toBe(180000.0);
    expect($parser->getTerminalLabel())->toBe('NPS6869');
    expect($parser->getPurpose())->toBe('thanh toan don hang');
});

it('can validate CRC checksum', function () {
    $validQr = '00020101021138570010A00000072701270006970403011300110123456780208QRIBFTTA53037045802VN63049E6F';
    $invalidQr = '00020101021138570010A00000072701270006970403011300110123456780208QRIBFTTA53037045802VN6304XXXX';

    expect(\Takashato\VietQr\VietQrParser::parse($validQr)->validateCrc())->toBeTrue();
    expect(\Takashato\VietQr\VietQrParser::parse($invalidQr)->validateCrc())->toBeFalse();
});

it('can convert parsed QR to array', function () {
    $qrString = '00020101021138570010A00000072701270006970403011300110123456780208QRIBFTTA53037045802VN63049E6F';
    $parser = \Takashato\VietQr\VietQrParser::parse($qrString);
    $array = $parser->toArray();

    expect($array)->toBeArray();
    expect($array['valid'])->toBeTrue();
    expect($array['is_napas'])->toBeTrue();
    expect($array['bank_bin'])->toBe('970403');
    expect($array['service_code'])->toBe('QRIBFTTA');
});

// ============================================
// QR PUSH (Merchant Payment) Tests
// ============================================

it('can create QR PUSH merchant payment', function () {
    $qr = VietQrCode::merchantPayment(
        bank: '970403',
        merchantId: '12345678',
        merchantName: 'Coffee Shop',
        merchantCity: 'Ho Chi Minh',
        mcc: '5812',
        amount: 50000,
    )->build();

    $parser = \Takashato\VietQr\VietQrParser::parse($qr);
    expect($parser->isValid())->toBeTrue();
    expect($parser->getServiceCode())->toBe('QRPUSH');
    expect($parser->getMerchantCategoryCode())->toBe('5812');
    expect($parser->getMerchantName())->toBe('Coffee Shop');
    expect($parser->getMerchantCity())->toBe('Ho Chi Minh');
    expect($parser->getAmount())->toBe(50000.0);
});

it('can set merchant name and city manually', function () {
    $qr = (new VietQrCode())
        ->staticMethod()
        ->withMerchant(fn(MerchantInfo $m) => $m
            ->forAccountTransfer('970403', '12345678'))
        ->merchantName('Test Store')
        ->merchantCity('Hanoi')
        ->build();

    $parser = \Takashato\VietQr\VietQrParser::parse($qr);
    expect($parser->getMerchantName())->toBe('Test Store');
    expect($parser->getMerchantCity())->toBe('Hanoi');
});

// ============================================
// Additional Info Fields Tests
// ============================================

it('can use all additional info fields', function () {
    // Use short values to keep total under 99 chars (TLV length limit)
    $qr = (new VietQrCode())
        ->staticMethod()
        ->withMerchant(fn(MerchantInfo $m) => $m->forAccountTransfer('970403', '12345678'))
        ->withAdditionalInfo(fn(AdditionalInfo $a) => $a
            ->billNumber('B1')
            ->mobileNumber('M1')
            ->storeLabel('S1')
            ->loyaltyNumber('L1')
            ->referenceLabel('R1')
            ->customerLabel('C1')
            ->terminalLabel('T1')
            ->purpose('P1')
            ->consumerDataRequest('A'))
        ->build();

    $parser = \Takashato\VietQr\VietQrParser::parse($qr);
    expect($parser->isValid())->toBeTrue();

    $additionalData = $parser->getAdditionalData();
    expect($additionalData['01'])->toBe('B1'); // Bill Number
    expect($additionalData['02'])->toBe('M1'); // Mobile Number
    expect($additionalData['03'])->toBe('S1'); // Store Label
    expect($additionalData['04'])->toBe('L1'); // Loyalty Number
    expect($additionalData['05'])->toBe('R1'); // Reference Label
    expect($additionalData['06'])->toBe('C1'); // Customer Label
    expect($additionalData['07'])->toBe('T1'); // Terminal Label
    expect($additionalData['08'])->toBe('P1'); // Purpose
    expect($additionalData['09'])->toBe('A'); // Consumer Data Request
});

// ============================================
// Tip/Convenience Fee Tests
// ============================================

it('can set tip indicator', function () {
    $qr = (new VietQrCode())
        ->staticMethod()
        ->withMerchant(fn(MerchantInfo $m) => $m->forAccountTransfer('970403', '12345678'))
        ->promptTip()
        ->build();

    expect($qr)->toContain('5502'); // Tip indicator ID
    expect($qr)->toContain('550201'); // Tip indicator = 01 (prompt)
});

it('can set fixed convenience fee', function () {
    $qr = (new VietQrCode())
        ->staticMethod()
        ->withMerchant(fn(MerchantInfo $m) => $m->forAccountTransfer('970403', '12345678'))
        ->convenienceFeeFixed(10000)
        ->build();

    expect($qr)->toContain('550202'); // Tip indicator = 02 (fixed)
    expect($qr)->toContain('5605'); // Convenience fee ID 56, length 05
    expect($qr)->toContain('10000');
});

it('can set percentage convenience fee', function () {
    $qr = (new VietQrCode())
        ->staticMethod()
        ->withMerchant(fn(MerchantInfo $m) => $m->forAccountTransfer('970403', '12345678'))
        ->convenienceFeePercentage(5.5)
        ->build();

    expect($qr)->toContain('550203'); // Tip indicator = 03 (percentage)
    expect($qr)->toContain('5703'); // Convenience fee % ID 57, length 03
    expect($qr)->toContain('5.5');
});

// ============================================
// Country Code Tests
// ============================================

it('can use countryCode method', function () {
    $qr = (new VietQrCode())
        ->staticMethod()
        ->withMerchant(fn(MerchantInfo $m) => $m->forAccountTransfer('970403', '12345678'))
        ->countryCode('VN')
        ->build();

    $parser = \Takashato\VietQr\VietQrParser::parse($qr);
    expect($parser->getCountryCode())->toBe('VN');
});

it('nation method is deprecated alias for countryCode', function () {
    $qr = (new VietQrCode())
        ->staticMethod()
        ->withMerchant(fn(MerchantInfo $m) => $m->forAccountTransfer('970403', '12345678'))
        ->nation('VN')
        ->build();

    $parser = \Takashato\VietQr\VietQrParser::parse($qr);
    expect($parser->getCountryCode())->toBe('VN');
});
