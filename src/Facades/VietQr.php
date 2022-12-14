<?php

namespace Takashato\VietQr\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Takashato\VietQR\VietQr
 */
class VietQr extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Takashato\VietQr\VietQr::class;
    }
}
