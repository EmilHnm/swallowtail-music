<?php

namespace App\Enum;

use MyCLabs\Enum\Enum;

class SongMetadataStatusEnum extends Enum
{
    const UPLOADING = "uploading";
    const UPLOADED = "uploaded";
    const PENDING = "pending";
    const PROCESSING = "processing";
    const DONE = "done";
    const ERROR = "error";
}
