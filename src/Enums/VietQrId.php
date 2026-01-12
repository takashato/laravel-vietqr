<?php

namespace Takashato\VietQr\Enums;

/**
 * Root-level data object IDs for VietQR codes.
 *
 * @see NAPAS VietQR Specification v1.5.2 Section 2
 */
enum VietQrId: string
{
    case FORMAT_INDICATOR = '00';
    case INITIATION_METHOD = '01';

    case MERCHANT_ACCOUNT_INFORMATION = '38';
    case MERCHANT_CATEGORY_CODE = '52';

    case TRANSACTION_CURRENCY = '53';
    case TRANSACTION_AMOUNT = '54';

    case TIP_OR_CONVENIENCE_INDICATOR = '55';
    case CONVENIENCE_FEE_FIXED = '56';
    case CONVENIENCE_FEE_PERCENTAGE = '57';

    case COUNTRY_CODE = '58';

    case MERCHANT_NAME = '59';
    case MERCHANT_CITY = '60';
    case POSTAL_CODE = '61';

    case ADDITIONAL_INFO = '62';
    case CRC = '63';

    case MERCHANT_INFO_LANGUAGE_TEMPLATE = '64';
}
