<?php

namespace Takashato\VietQr\Enums;

enum VietQrId: string
{
    case FORMAT_INDICATOR = '00';
    case INITIATION_METHOD = '01';

    case MERCHANT_ACCOUNT_INFORMATION = '38';
    case MERCHANT_CATEGORY_CODE = '52';

    case TRANSACTION_CURRENCY = '53';
    case TRANSACTION_AMOUNT = '54';

    case NATION = '58';

    case ADDITIONAL_INFO = '62';
    case CRC = '63';
}
