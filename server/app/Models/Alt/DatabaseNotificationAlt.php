<?php

namespace App\Models\Alt;

use App\Models\Traits\AdvancedFilters;
use Illuminate\Notifications\DatabaseNotification;
use Orchid\Screen\AsSource;

class DatabaseNotificationAlt extends DatabaseNotification
{
    use AdvancedFilters;
    use AsSource;
}
