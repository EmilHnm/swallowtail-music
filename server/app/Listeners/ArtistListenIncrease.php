<?php

namespace App\Listeners;

use App\Events\ListenIncrease;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class ArtistListenIncrease
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
        $song = $event->song;
        $artists = $song->artist()->get();
        foreach ($artists as $artist) {
            $artist->increment("listens");
        }
    }
}
