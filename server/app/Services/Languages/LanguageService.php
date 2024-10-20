<?php

namespace App\Services\Languages;

use App\Services\Languages\KoreanLanguageService;
use App\Services\Languages\ChineseLanguageService;
use App\Services\Languages\JapaneseLanguageService;

class LanguageService
{

    public function __construct(
        private JapaneseLanguageService $japaneseLanguageService,
        private KoreanLanguageService $koreanLanguageService,
        private ChineseLanguageService $chineseLanguageService,
        private LatinLanguageService $latinLanguageService
    ) {}

    public function detechLanguage($string)
    {
        if ($this->japaneseLanguageService->isJapanese($string)) {
            return $this->japaneseLanguageService;
        }

        if ($this->koreanLanguageService->isKorean($string)) {
            return $this->koreanLanguageService;
        }

        if ($this->chineseLanguageService->isChinese($string)) {
            return $this->chineseLanguageService;
        }

        return $this->latinLanguageService;
    }

    public function getPhoneticScripts($string)
    {
        return $this->detechLanguage($string)->phoneticToScripts($string);
    }

}
