<?php

namespace App\Events;

use App\Enum\ResponseStatusEnum;
use App\Models\Request;
use App\Models\Response;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ReviewResponseEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels, BroadcastAsNotification;

    /**
     * Create a new event instance.
     */
    public function __construct(public readonly Response $response)
    {
        //
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        if($this->response->status === ResponseStatusEnum::REJECTED) {
            return [
                new PrivateChannel('notifications.' . $this->response->responder),
            ];
        } elseif($this->response->status === ResponseStatusEnum::APPROVED) {
            $request = $this->response->request()->first();
            return $request->responses()->get()->map(function(Response $response) {
                return new PrivateChannel('notifications.' . $response->responder);
            })->toArray();
        }
        return  [];
    }
    


}
