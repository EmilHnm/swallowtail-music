<?php

namespace App\Listeners;

use App\Events\ResponseRequestEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class ResponseRequestListener
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
    public function handle(ResponseRequestEvent $event): void
    {
        //
        $target = $event->response->request->requester()->first();
        $target->notify(new \App\Notifications\ResponseNotification($event->response));
    }
}
