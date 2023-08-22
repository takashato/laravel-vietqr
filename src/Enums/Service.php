<?php

namespace Takashato\VietQr\Enums;

enum Service: string
{
    /**
     * Inter-Bank Fund Transfer 24/7 to Account service by QR
     */
    case NAPAS_BY_ACCOUNT = 'QRIBFTTA';

    /**
     * Inter-Bank Fund Transfer 24/7 to Card service by QR
     */
    case NAPAS_BY_CARD = 'QRIBFTTC';

    /**
     * Cash withdraw service at ATM by QR
     */
    case QR_CASH = 'QRCASH';

    /**
     * Product payment service by QR
     */
    case QR_PUSH = 'QRPUSH';
}
