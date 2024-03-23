<?php

namespace App\Providers;

use App\Events\ResponseRequestEvent;
use App\Events\ReviewResponseEvent;
use App\Events\SongConvertedSuccessFull;
use App\Events\SystemNotificationEvent;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],

        \App\Events\ListenIncrease::class => [
            \App\Listeners\ArtistListenIncrease::class,
            \App\Listeners\TotalPlayedIncrease::class,
        ],

        SongConvertedSuccessFull::class => [
            \App\Listeners\SendSongConvertedSuccessFullListener::class,
        ],

        SystemNotificationEvent::class => [
            \App\Listeners\SystemNotificationListener::class,
        ],

        ResponseRequestEvent::class => [
            \App\Listeners\ResponseRequestListener::class,
        ],

        ReviewResponseEvent::class => [
            \App\Listeners\ReviewResponseListener::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     *
     * @return bool
     */
    public function shouldDiscoverEvents()
    {
        return false;
    }
}
