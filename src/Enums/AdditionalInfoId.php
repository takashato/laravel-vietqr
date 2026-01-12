<?php

namespace Takashato\VietQr\Enums;

/**
 * Sub-field IDs for Additional Data Field Template (ID 62).
 *
 * @see NAPAS VietQR Specification v1.5.2 Section 4
 */
enum AdditionalInfoId: string
{
    case BILL_NUMBER = '01';
    case MOBILE_NUMBER = '02';
    case STORE_LABEL = '03';
    case LOYALTY_NUMBER = '04';
    case REFERENCE_LABEL = '05';
    case CUSTOMER_LABEL = '06';
    case TERMINAL_LABEL = '07';
    case PURPOSE = '08';
    case CONSUMER_DATA_REQUEST = '09';
}
