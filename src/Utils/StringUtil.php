<?php

namespace Takashato\VietQr\Utils;

use InvalidArgumentException;

class StringUtil
{
    public static function buildWithLength(string $id, string $content, int $maxLength = 99): string
    {
        $length = strlen($content);

        if ($length > $maxLength) {
            throw new InvalidArgumentException(
                "Content length ($length) exceeds maximum allowed ($maxLength) for TLV field ID $id"
            );
        }

        $paddedLength = str_pad($length, 2, '0', STR_PAD_LEFT);

        return str_pad($id, 2, '0', STR_PAD_LEFT)
            . $paddedLength
            . $content;
    }
}
