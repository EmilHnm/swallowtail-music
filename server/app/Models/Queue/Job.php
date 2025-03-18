<?php

namespace App\Models\Queue;

use App\Models\Traits\AdvancedFilters;
use Illuminate\Database\Eloquent\Model;
use Orchid\Screen\AsSource;

class Job extends Model
{
    use AdvancedFilters, AsSource;

    protected $dateFormat = 'Y-m-d H:i:s';

    protected $table = 'jobs';

    protected $casts = [
        'created_at' => 'datetime',
        'available_at' => 'datetime',
        'payload' => 'array',
    ];
}
