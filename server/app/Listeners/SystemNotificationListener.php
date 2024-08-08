<?php

namespace App\Listeners;

use App\Events\SystemNotificationEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SystemNotificationListener
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
    public function handle(SystemNotificationEvent $event): void
    {
        //
        $targetUser = $event->user;
        $data = $event->data;

        $targetUser->notify(new \App\Notifications\SystemNotification(
            $data['message'],
            $data['title'],
            $data['link'],
            $data['icon']
        ));
    }
}
