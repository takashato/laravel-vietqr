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
        ->toEqual('00020101021238570010A00000072701270006970403011300110123456780208QRIBFTTA530370454061800005802VN62340107NPS68690819thanh toan don hang63042E2E');
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
        ->toEqual('00020101021238600010A00000072701300006970403011697040311012345670208QRIBFTTC530370454061800005802VN62340107NPS68690819thanh toan don hang6304A203');
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
        purpose: 'thanh toan don hang'
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