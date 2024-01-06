<?php

namespace App\Orchid\Helpers;
// @deprecated
class FileSize
{
    public static function human($bytes, $decimals = 2)
    {
        $sz = 'BKMGTP';
        $factor = floor((strlen($bytes) - 1) / 3);
        return sprintf("%.{$decimals}f", $bytes / (1024 ** $factor)) . @$sz[$factor];
    }
}
