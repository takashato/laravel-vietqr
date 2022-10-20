<?php

namespace Takashato\VietQr;

class VietQr
{
    public function create(): VietQrCode
    {
        return new VietQrCode();
    }
}
