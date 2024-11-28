<?php

namespace Takashato\VietQr\Utils;

class StringUtil
{
    public static function buildWithLength(string $id, string $content, int $maxLength = 99): string
    {
        $length = strlen($content);
        $paddedLength = str_pad($length, ceil(log10($maxLength)), '0', STR_PAD_LEFT);

        return str_pad($id, 2, '0', STR_PAD_LEFT)
            . $paddedLength
            . $content;
    }
}
