<?php

namespace App\Models\Queue;

use App\Models\Traits\AdvancedFilters;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Orchid\Screen\AsSource;

class Monitor extends \romanzipp\QueueMonitor\Models\Monitor
{
    use AdvancedFilters;
    use AsSource;
    use HasFactory;
}
