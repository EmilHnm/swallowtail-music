<?php

namespace App\Listeners;

use App\Events\ListenIncrease;
use App\Http\Controllers\StatisticController;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class TotalPlayedIncrease
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(ListenIncrease $event): void
    {
        //
        app(StatisticController::class)->saveTotalPlayedTime();
    }
}
