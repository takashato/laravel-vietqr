<?php

use Takashato\VietQr\Data\AdditionalInfo;
use Takashato\VietQr\Data\MerchantInfo;
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
            ->build()
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
            ->build()
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
            ->build()
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
            ->build()
    )
        ->toBeString()
        ->toEqual('00020101021238600010A00000072701300006970403011697040311012345670208QRIBFTTC530370454061800005802VN62340107NPS68690819thanh toan don hang6304A203');
});
