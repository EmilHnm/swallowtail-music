<?php
namespace App\Services\Languages;

use Transliterator;

class KoreanLanguageService
{
    public function isKorean(string $string): bool
    {
        return preg_match("/[\p{Hangul}]/ui", $string);
    }

    public function phoneticToScripts(string $string): string
    {
        $transliterator = Transliterator::create('Any-Latin; Latin-ASCII');
        $string = $transliterator->transliterate($string);
        $string = strtolower($string);
        return $string;
    }
}
