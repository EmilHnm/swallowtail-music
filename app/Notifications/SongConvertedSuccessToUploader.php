<?php

namespace App\Notifications;

use App\Enum\NotificationIconEnum;
use App\Models\Song;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use romanzipp\QueueMonitor\Traits\IsMonitored;

class SongConvertedSuccessToUploader extends Notification implements ShouldQueue
{
    use Queueable;
    use IsMonitored;

    /**
     * Create a new notification instance.
     */
    public function __construct(protected readonly Song $song)
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->line('The introduction to the notification.')
            ->action('Notification Action', url('/'))
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
            "title" => "Song converted Successfully",
            "message" => "Your uploaded song <strong>{$this->song->title}</strong> has been converted successfully. It's now ready to publish!",
            "icon" => NotificationIconEnum::SUCCESS,
            "link" => "",
        ];
    }
}
