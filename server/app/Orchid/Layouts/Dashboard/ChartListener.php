<?php

namespace App\Orchid\Layouts\Dashboard;

use App\Http\Controllers\admin\StatisticAdminController;
use App\Models\WebStatistic;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Orchid\Screen\Fields\DateTimer;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Layouts\Listener;
use Orchid\Screen\Repository;
use Orchid\Support\Facades\Layout;

class ChartListener extends Listener
{
    use StatisticAdminController;
    /**
     * List of field names for which values will be listened.
     *
     * @var string[]
     */
    protected $targets = ['start', 'end'];

    /**
     * The screen's layout elements.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    protected function layouts(): iterable
    {
        $start = $this->query->get('start') ?
            Carbon::make($this->query->get('start'))->toDateString() :
            now()->subDays(8)->toDateString();
        $end = $this->query->get('end') ?
            Carbon::make($this->query->get('end'))->subDays()->toDateString() :
            now()->toDateString();
        return [
            Layout::block([
                Layout::blank([
                    Layout::columns([
                        Layout::rows([
                            Input::make('start')
                                ->type('date')
                                ->title('From Date')
                                ->value($start)
                                ->required(),
                        ]),
                        Layout::rows([
                            Input::make('end')
                                ->type('date')
                                ->title('To Date')
                                ->value($end)
                                ->required(),
                        ]),
                    ]),
                ]),
                Layout::blank([
                    Layout::columns([
                        ChartLayout::make('dataset.total_users', 'Total Users')
                            ->description("Total Users from $start to $end"),
                        ChartLayout::make('dataset.total_sessions', 'Total Sessions')
                            ->description("Total Sessions from $start to $end"),
                        ChartLayout::make('dataset.total_sessions_duration', 'Total Sessions Duration')
                            ->description("Total Sessions Duration from $start to $end"),
                        ChartLayout::make('dataset.total_requests', 'Total Requests')
                            ->description("Total Requests from $start to $end"),
                    ]),
                    Layout::columns([
                        ChartLayout::make('dataset.total_songs', 'Total Songs')
                            ->description("Total Songs from $start to $end"),
                        ChartLayout::make('dataset.total_user_upload_songs', 'Total User Upload Songs')
                            ->description("Total User Upload Songs from $start to $end"),
                        ChartLayout::make('dataset.total_played_time', 'Total Played Time')
                            ->description("Total Played Time from $start to $end"),
                        ChartLayout::make('dataset.total_played_duration', 'Total Played Duration')
                            ->description("Total Played Duration from $start to $end"),
                    ]),
                    Layout::columns([
                        ChartLayout::make('dataset.total_albums', 'Total Albums')
                            ->description("Total Albums from $start to $end"),
                        ChartLayout::make('dataset.total_artists', 'Total Artists')
                            ->description("Total Artists from $start to $end"),
                        ChartLayout::make('dataset.total_genres', 'Total Genres')
                            ->description("Total Genres from $start to $end"),
                        ChartLayout::make('dataset.total_playlists', 'Total Playlists')
                            ->description("Total Playlists from $start to $end"),
                    ]),
                ])->canSee($this->query->has('dataset')),
            ])->vertical()->title('Longtime Statistic'),
        ];
    }

    /**
     * Update state
     *
     * @param \Orchid\Screen\Repository $repository
     * @param \Illuminate\Http\Request  $request
     *
     * @return \Orchid\Screen\Repository
     */
    public function handle(Repository $repository, Request $request): Repository
    {
        $start = $request->get('start');
        $end = $request->get('end');

        $dataset = $this->calculateRecord($start,$end);

        return $repository->set('start', $start)
            ->set('end', $end)
            ->set('dataset', $dataset)
            ->set('dataset.total_users', [$dataset->total_users])
            ->set('dataset.total_sessions', [$dataset->total_sessions])
            ->set('dataset.total_sessions_duration', [$dataset->total_sessions_duration])
            ->set('dataset.total_requests', [$dataset->total_requests])

            ->set('dataset.total_songs', [$dataset->total_songs])
            ->set('dataset.total_user_upload_songs', [$dataset->total_user_upload_songs])
            ->set('dataset.total_played_time', [$dataset->total_played_time])
            ->set('dataset.total_played_duration', [$dataset->total_played_duration])

            ->set('dataset.total_albums', [$dataset->total_albums])
            ->set('dataset.total_artists', [$dataset->total_artists])
            ->set('dataset.total_genres', [$dataset->total_genres])
            ->set('dataset.total_playlists', [$dataset->total_playlists])
            ;
    }
}
