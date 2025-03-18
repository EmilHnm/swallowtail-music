<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Contracts\Filesystem\Filesystem;

class StorageManager
{
    private static $defaultDisk = '';

    private static Filesystem $storage;
    public function __construct()
    {
        self::setDisk();
    }

    public static function getDisk()
    {
        if (self::$defaultDisk == '') {
            self::setDisk();
        }
        return self::$defaultDisk;
    }

    public static function setDisk()
    {
        self::$defaultDisk = env('DISK_DEFAULT_ORIGINAL', 'local');
        self::$storage = Storage::disk(self::$defaultDisk);
    }

    public static function saveFilePath($basePath)
    {
        $basePath = (new self())->saveFolderPath($basePath);
        $directories_count = count(self::$storage->allDirectories($basePath));
        $files_count = count(self::$storage->allFiles($basePath . "/" . str_pad($directories_count - 1, 3, '0', STR_PAD_LEFT) . '/'));
        if ($files_count < 100 && $directories_count > 0) {
            return $basePath . "/" . str_pad(($directories_count - 1), 3, '0', STR_PAD_LEFT) . "/";
        } else {
            return $basePath . "/" . str_pad($directories_count, 3, '0', STR_PAD_LEFT) . "/";
        }
    }

    private function saveFolderPath($basePath)
    {
        $directories_count = count(self::$storage->directories($basePath));
        $sub_directories_count = count(self::$storage->directories($basePath . "/" . str_pad($directories_count - 1, 3, '0', STR_PAD_LEFT) . '/'));
        if ($sub_directories_count < 100 && $directories_count > 0) {
            return $basePath . "/" . str_pad(($directories_count - 1), 3, '0', STR_PAD_LEFT) . "/";
        } else {
            return $basePath . "/" . str_pad($directories_count, 3, '0', STR_PAD_LEFT) . "/";
        }
    }
}
