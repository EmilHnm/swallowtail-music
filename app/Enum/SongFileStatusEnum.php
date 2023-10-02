<?php

namespace App\Enum;

use MyCLabs\Enum\Enum;

class SongFileStatusEnum extends Enum
{
    const UPLOADING = "uploading";
    const UPLOADED = "uploaded";
    const PENDING = "pending";
    const PROCESSING = "prcessing";
    const DONE = "done";
    const ERROR = "error";
}
