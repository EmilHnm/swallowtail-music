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
    const ERROR = "error";
    const DUPLICATED = "duplicated";
}
