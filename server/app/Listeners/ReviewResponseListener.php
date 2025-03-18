<?php

namespace App\Listeners;

use App\Enum\ResponseStatusEnum;
use App\Events\ReviewResponseEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class ReviewResponseListener
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
    public function handle(ReviewResponseEvent $event): void
    {
        //
        $response = $event->response;
        if($response->status === ResponseStatusEnum::REJECTED) {
            $response->responder()->first()->notify(new \App\Notifications\ReviewResponseNotification($response));
        } elseif ($response->status === ResponseStatusEnum::APPROVED) {
            $request = $response->request()->first();
            $request->responses()->get()->map(function($response) {
                if ($response->responder !== $response->request()->first()->requester) {
                    $response->responder()->first()->notify(new \App\Notifications\ReviewResponseNotification($response));
                } elseif ($response->responder === $response->request()->first()->requester) {
                    $response->request()->first()->requester()->first()->notify(new \App\Notifications\ResponseApprovedNotification($response));
                }
            });
        }
    }
}
