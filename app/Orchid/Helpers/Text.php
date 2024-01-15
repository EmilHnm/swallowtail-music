<?php

namespace App\Orchid\Helpers;

use App\Models\Document;
use App\ServerSdk\Enum\WebDocumentStatus;

class Text
{
    public static function limit($text, $limit = 30, $end = '...') : string {
        $show = \Str::limit($text, $limit, $end);
        return "<span title='".$text."'>".$show."</span>";
    }

    public static function normalize(?string $string) {
        $string = trim( $string );
        $string = mb_strtolower( $string );
        $string = preg_replace( "/\p{Z}+/ui", " ", $string);
        $string = preg_replace( "/[^\p{M}\w\s]+/ui", " ", $string);
        $string = preg_replace( "/\s{2,}/", " ", $string);
        return trim($string);
    }
}
