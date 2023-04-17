<?php

class StorageManager
{
    private static $defaultDisk = 'local';

    public function __construct()
    {
        self::$defaultDisk = env('DISK_DEFAULT_ORIGINAL', 'local');
    }

    public static function getDisk($disk = null)
    {
        if ($disk == null) {
            $disk = self::$defaultDisk;
        }
        if (self::$defaultDisk == 'local') {
            return "final_audio";
        }
        return self::$defaultDisk;
    }
}
