<?php

namespace App\Orchid\Screens;

use App\Orchid\Helpers\Date;
use App\Orchid\Helpers\ListingUrl;
use App\Orchid\Screens\Traits\HasDumpModelModal;
use App\Orchid\Screens\Traits\HasShowHideCountingToggle;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Str;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Fields\DateRange;
use Orchid\Screen\Screen;
use Orchid\Screen\TD;
use Orchid\Support\Facades\Layout;
use App\Models\Alt\DatabaseNotificationAlt;

class NotificationListScreen extends Screen
{
    use HasDumpModelModal, HasShowHideCountingToggle;

    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        $query = DatabaseNotificationAlt::with('notifiable')
            ->advancedFilter([
                'id',
                'type',
                'notifiable_type',
                'notifiable_id',
                'read_at:date_range',
                'created_at:date_range',
            ], [
                'type',
                'notifiable_type',
                'notifiable_id',
                'read_at',
                'created_at',
            ]);

        return [
            'notifications' => $this->paging($query),
        ];
    }

    /**
     * The name of the screen displayed in the header.
     */
    public function name(): ?string
    {
        return 'Notification List Screen';
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [];
    }

    /**
     * The screen's layout elements.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout(): iterable
    {
        return [
            $this->getDumpModal(),
            Layout::table('notifications', [
                TD::make('id')
                    ->render(fn ($n) => $n->id.self::getDumpModelToggle(DatabaseNotification::class, $n->id))
                    ->filter(),
                TD::make('type')
                    ->render(
                        fn ($n) => Link::make(Str::after($n->type, "App\Notifications"))
                            ->href(ListingUrl::make(['type' => $n->type]))
                    )
                    ->sort()
                    ->filter()
                    ->filterValue(fn ($type) => Str::after($type, "App\Notifications")),
                TD::make('notifiable_type')
                    ->render(fn ($n) => $n->notifiable_type)
                    ->sort()
                    ->filter(),
                TD::make('notifiable_id')
                    ->render(ListingUrl::cell('notifiable_id', sort: '-created_at', text_field: 'notifiable.email'))
                    ->sort()
                    ->filter(),
                TD::make('data', 'Content')
                    ->width(300)
                    ->render(function (DatabaseNotificationAlt $n) {
                        if (method_exists($n->type, 'getMessage')) {
                            $html = $n->type::getMessage($n);
                        } else {
                            $html = '';
                            foreach ($n->data as $k => $v) {
                                if (is_string($v))
                                    $html .= $k.' : '.Str::limit($v, 20).'<br/>';
                                else {
                                    $html .= $k.' : '.json_encode($v).'<br/>';
                                }
                            }
                        }

                        return $html;
                    }),
                TD::make('read_at')
                    ->sort()
                    ->filter(DateRange::make())
                    ->render(Date::human('read_at')),
                TD::make('created_at')
                    ->sort()
                    ->filter(DateRange::make())
                    ->render(Date::human('created_at')),
            ]),
        ];
    }
}
