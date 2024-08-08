<?php

namespace App\Orchid\Helpers;

use Illuminate\Http\Response;

class Common
{

    private const VALID_COLUMN_NAME_REGEX = '/^(?![\d])[\.A-Za-z0-9_>-]*$/';

    public static function sanitize(string $column): string
    {
        abort_unless(preg_match(self::VALID_COLUMN_NAME_REGEX, $column), Response::HTTP_BAD_REQUEST);

        return $column;
    }
}
