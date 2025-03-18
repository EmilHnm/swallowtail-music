<?php

namespace App\Notifications;

use App\Enum\NotificationIconEnum;
use App\Enum\RequestStatusEnum;
use App\Enum\ResponseStatusEnum;
use App\Models\Request;
use App\Models\Response;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReviewResponseNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    protected $responder;
    protected Request $request;
    public function __construct(protected readonly Response $response)
    {
        //
        if ($this->response->request) {
            $this->request = $this->response->request;
        }
        if ($this->response->responder) {
            $this->responder = $this->response->responder;
        }
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
        if ($this->response->status === ResponseStatusEnum::REJECTED && $this->request->status === RequestStatusEnum::PENDING) {
            return [
                "title" => "Response Rejected!",
                "message" => "Your response in request [{$this->request->title}] has been rejected by the requester. Click to view details.",
                "icon" => NotificationIconEnum::WARNING,
                "link" => [
                    "name" => "accountRequestDetails",
                    "params" => [
                        "id" => $this->request->id,
                    ],
                ],
            ];
        }
        return  [
            "title" => "Request Resolved!",
            "message" => "A request [{$this->request->title}] has been resolved. Click to view details.",
            "icon" => NotificationIconEnum::WARNING,
            "link" => [
                "name" => "accountRequestDetails",
                "params" => [
                    "id" => $this->request->id,
                ],
            ],
        ];
    }
}
