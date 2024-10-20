<?php
namespace App\Services\Languages;

class LatinLanguageService
{
    public function isLatin(string $string): bool
    {
        return preg_match("/[\p{Latin}]/ui", $string);
    }

    public function phoneticToScripts(string $string): string
    {
        return $string;
    }
}
