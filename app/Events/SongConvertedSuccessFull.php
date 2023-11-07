<?php

namespace App\Events;

use App\Models\Song;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class SongConvertedSuccessFull implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public User $user;
    public Song $song;
    /**
     * Create a new event instance.
     */
    public function __construct(User $user, Song $song)
    {
        //
        $this->user = $user;
        $this->song = $song;
    }
    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('notifications.' . $this->user->user_id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'notifications';
    }

    public function broadcastWith(): array
    {
        return [
            'status' => 'success',
        ];
    }
}
