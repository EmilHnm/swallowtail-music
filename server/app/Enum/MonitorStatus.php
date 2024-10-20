<?php

namespace App\Enum;

use MyCLabs\Enum\Enum;

class MonitorStatus extends Enum
{
    public const RUNNING = 0;

    public const SUCCEEDED = 1;

    public const FAILED = 2;

    public const STALE = 3;

    public const QUEUED = 4;

    public static function renderStatus($value)
    {
        switch ($value) {
            case self::RUNNING:
                return "<span class='px-3 rounded-5 py-1 bg-primary'>".self::search($value).'</span>';
            case self::SUCCEEDED:
                return "<span class='px-3 rounded-5 py-1 bg-success'>".self::search($value).'</span>';
            case self::FAILED:
                return "<span class='px-3 rounded-5 py-1 bg-danger'>".self::search($value).'</span>';
            case self::STALE:
                return "<span class='px-3 rounded-5 py-1 bg-dark'>".self::search($value).'</span>';
            case self::QUEUED:
                return "<span class='px-3 rounded-5 py-1 bg-warning'>".self::search($value).'</span>';
        }
    }
}
