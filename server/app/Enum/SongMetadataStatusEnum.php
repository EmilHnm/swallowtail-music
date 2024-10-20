<?php

namespace App\Enum;

use MyCLabs\Enum\Enum;
use Predis\Command\Redis\PUBLISH;

class SongMetadataStatusEnum extends Enum
{
    const UPLOADING = "uploading";
    const UPLOADED = "uploaded";
    const PENDING = "pending";
    const PROCESSING = "processing";
    const DONE = "done";
    const PUBLISH = "publish";

//    error status
    const ERROR = "error";
    const ERROR_DUPLICATED = "error_duplicated";
    const ERROR_BANNED = "error_banned";
    const ERROR_DISCRIMINATION = "error_discrimination";
    const ERROR_COPYRIGHT = "error_copyright";

    /**
     * Get the error message for a given status.
     *
     * @param string $status
     * @return string
     */
    public static function getErrorMessage($status)
    {
        switch ($status) {
            case self::ERROR:
                return "Error";
            case self::ERROR_DUPLICATED:
                return "Song already exists";
            case self::ERROR_BANNED:
                return "Song contains banned words or phrases";
            case self::ERROR_DISCRIMINATION:
                return "Song contains discriminatory content";
            case self::ERROR_COPYRIGHT:
                return "Song contains copyrighted content or is not allowed to be uploaded";
            default:
                return "Unknown";
        }
    }

    public static function notReadyStatus()
    {
        return [
            self::UPLOADING,
            self::UPLOADED,
            self::PENDING,
            self::PROCESSING
        ];
    }
}
