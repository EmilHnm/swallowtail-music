<?php

namespace App\Listeners;

use App\Events\SongConvertedSuccessFull;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendSongConvertedSuccessFullListener
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
    public function handle(SongConvertedSuccessFull $event): void
    {
        //
        $owner = $event->user;
        $song = $event->song;

        $owner->notify(new \App\Notifications\SongConvertedSuccessToUploader($song));
    }
}
