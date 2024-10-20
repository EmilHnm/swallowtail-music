<?php
namespace App\Services\Languages;

class ChineseLanguageService
{
    public function isChinese(string $string): bool
    {
        return preg_match("/[\p{Han}]/ui", $string);
    }
}
