<?php

namespace App\Models\Queue;

use App\Models\Traits\AdvancedFilters;
use Illuminate\Database\Eloquent\Model;
use Orchid\Screen\AsSource;

class QueueFailedJob extends Model
{
    use AdvancedFilters, AsSource;

    protected $dateFormat = 'Y-m-d H:i:s';

    protected $casts = [
        'failed_at' => 'datetime',
        'payload' => 'array',
    ];

    protected $table = 'failed_jobs';
}
