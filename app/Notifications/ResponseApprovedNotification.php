<?php

namespace App\Notifications;

use App\Enum\NotificationIconEnum;
use App\Models\Request;
use App\Models\Response;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResponseApprovedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $responder;
    protected Request $request;

    /**
     * Create a new notification instance.
     */
    public function __construct(protected readonly Response $response)
    {
        //
        $this->responder = $response->responder()->first();
        $this->request = $response->request;
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
            "title" => "Response Approved!",
            "message" => "Your response to request {$this->responder->name} has been approved. Click to view details.",
            "icon" => NotificationIconEnum::INFO,
            "link" => [
                "name" => "accountRequestDetails",
                "params" => [
                    "id" => $this->request->id,
                ],
            ],
        ];
    }
}
