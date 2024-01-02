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
}