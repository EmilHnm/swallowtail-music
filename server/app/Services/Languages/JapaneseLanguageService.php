<?php

namespace App\Services\Languages;

use Transliterator;

class JapaneseLanguageService
{
    public function isJapanese(string $string): bool
    {
        //http://www.rikai.com/library/kanjitables/kanji_codes.unicode.shtml
        return preg_match("/[\p{Hiragana}\p{Katakana}\p{Han}]\s?[\p{Hiragana}\p{Katakana}\p{Han}]/ui", $string);
    }

    public function toKatakana(string $string): string
    {
        return exec( 'echo '.$string.' | mecab  -O yomi');
    }

    function phoneticToScripts(string $string): string
    {
        $transliterator = Transliterator::create('Any-Latin; Latin-ASCII');
        $string = $transliterator->transliterate($this->toKatakana($string));
        $string = strtolower($string);
        return $string;
    }

}
