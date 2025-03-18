<?php

namespace App\Enum;

use MyCLabs\Enum\Enum;

class RequestStatusEnum extends Enum
{
    const PENDING = 'pending';
    const RESOLVED = 'resolved';
    const DENIED = 'denied';
    const CANCELLED = 'cancelled';
}
