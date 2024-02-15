<?php

namespace App\Enum;

use MyCLabs\Enum\Enum;

class ResponseStatusEnum extends Enum
{
    const RESPONDED = 'responded';
    const APPROVED = 'approved';
    const REJECTED = 'rejected';
}
