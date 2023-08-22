<?php

namespace Takashato\VietQr;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class VietQrServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('vietqr');

        $this->app->bind(VietQr::class, function () {
            return new VietQr();
        });

        $this->app->alias(\Takashato\VietQr\Facades\VietQr::class, 'VietQr');
    }
}
