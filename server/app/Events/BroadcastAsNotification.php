<?php
namespace App\Events;
trait BroadcastAsNotification
{
    public function broadcastAs(): string
    {
        return config('broadcasting.notifications');
    }

    public function broadcastWith(): array
    {
        return [
            'status' => 'success',
        ];
    }
}
