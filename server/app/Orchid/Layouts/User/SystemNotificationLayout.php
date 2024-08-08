<?php

namespace App\Orchid\Layouts\User;

use App\Enum\NotificationIconEnum;
use Orchid\Screen\Field;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Fields\TextArea;
use Orchid\Screen\Layouts\Rows;

class SystemNotificationLayout extends Rows
{
    /**
     * Used to create the title of a group of form elements.
     *
     * @var string|null
     */
    protected $title;

    /**
     * Get the fields elements to be displayed.
     *
     * @return Field[]
     */
    protected function fields(): iterable
    {
        return [
            Input::make('user_id')
                ->type('text')
                ->required()
                ->title('User ID')
                ->placeholder('User ID')
                ->help('User ID to send a notification to.')
                ->readonly()
                ->value(optional(request()->user())->id),
             Input::make('title')
                ->type('text')
                ->required()
                ->title('Title')
                ->placeholder('Title')
                ->value('System Notification')
                ->help('The title of the notification.'),
            Select::make('icon')
                ->required()
                ->title('Icon')
                ->options(NotificationIconEnum::toArray())
                ->empty('Select an icon')
                ->help('The icon of the notification.'),
            TextArea::make('message')
                ->required()
                ->title('Message')
                ->placeholder('Message')
                ->help('The message to be sent to the user.'),
        ];
    }
}
