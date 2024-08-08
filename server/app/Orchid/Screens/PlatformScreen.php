<?php

declare(strict_types=1);

namespace App\Orchid\Screens;

use App\Http\Controllers\admin\StatisticAdminController;
use App\Models\WebStatistic;
use App\Orchid\Layouts\Dashboard\ChartListener;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Illuminate\Support\Facades\Redis;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;

class PlatformScreen extends Screen
{
    use StatisticAdminController;
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        // get yesterday data from database

        return [
            'current_status' => $this->getCurrentData(),
            'yesterday_status' => $this->getYesterdayData(),
        ];
    }

    /**
     * The name of the screen displayed in the header.
     */
    public function name(): ?string
    {
        return config('app.name') . ' Dashboard';
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
     * @return \Orchid\Screen\Layout[]
     */
    public function layout(): iterable
    {
        return [
            Layout::blank([
                Layout::metrics([
                    'Total Played Times Now'    => 'current_status.total_played_time',
                    'Total Played Duration Now' => 'current_status.total_played_duration',
                    'Total Sessions Duration Now' => 'current_status.total_sessions_duration',
                    'Total Requests Now' => 'current_status.total_requests',
                ])->title('Current Status'),
                Layout::metrics([
                    'Users ' => 'yesterday_status.total_users',
                    'Sessions ' => 'yesterday_status.total_sessions',
                    'Sessions Duration ' => 'yesterday_status.total_sessions_duration',
                    'Requests ' => 'yesterday_status.total_requests',
                ])->title('Yesterday\'s Status'),
                Layout::metrics([
                    'Songs ' => 'yesterday_status.total_songs',
                    'User Upload Songs ' => 'yesterday_status.total_user_upload_songs',
                    'Played Time ' => 'yesterday_status.total_played_time',
                    'Played Duration ' => 'yesterday_status.total_played_duration',
                ]),
                Layout::metrics([
                    'Albums ' => 'yesterday_status.total_albums',
                    'Artists ' => 'yesterday_status.total_artists',
                    'Genres ' => 'yesterday_status.total_genres',
                    'Playlists ' => 'yesterday_status.total_playlists',
                ]),
            ]),
            ChartListener::class,
        ];
    }
}
