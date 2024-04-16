<?php

namespace App\Http\Controllers\admin;

use App\Models\WebStatistic;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Illuminate\Support\Facades\Redis;

trait StatisticAdminController
{
    protected function getCurrentData(): array
    {
        $current_data_keys = [
            'total_played_time',
            'total_played_duration',
            'total_requests',
            'total_sessions_duration'
        ];

        $current_data_values = Redis::mget(array_map(function ($key) {
            return Carbon::today()->timestamp . '_' . $key;
        }, $current_data_keys));

        return [
            'total_played_time' => [
                'value' => number_format(+$current_data_values[0] ?? 0) . ' times'
            ],
            'total_played_duration' => [
                'value' => CarbonInterval::seconds(+$current_data_values[1] ?? 0)->cascade()->floorMinutes()
            ],
            'total_requests' => [
                'value' => number_format(+$current_data_values[2] ?? 0) . ' requests'
            ],
            'total_sessions_duration' => [
                'value' => CarbonInterval::seconds(+$current_data_values[3] ?? 0)->cascade()->floorMinutes()
            ],
        ];
    }


    protected function getYesterdayData(): array
    {
        $yesterday_data = WebStatistic::whereDate('created_at', Carbon::yesterday())->first();
        $the_day_before_yesterday_data = WebStatistic::whereDate('created_at', Carbon::yesterday()->subDay())->first();

        $default_values = [
            'total_users', 'total_sessions', 'total_sessions_duration', 'total_requests',
            'total_songs', 'total_user_upload_songs', 'total_played_time', 'total_played_duration',
            'total_albums', 'total_artists', 'total_genres', 'total_playlists'
        ];

        foreach ($default_values as $value) {
            ${"yesterday_$value"} = $yesterday_data ? $yesterday_data->$value : 0;
            ${"yesterday_{$value}_change"} = $yesterday_data ? ((${"yesterday_$value"} / ($the_day_before_yesterday_data?->$value ? $the_day_before_yesterday_data->$value : 1 )) * 100) : 0;
            if (${"yesterday_$value"} - ($the_day_before_yesterday_data?->$value ?? 0) < 0) {
                ${"yesterday_{$value}_change"} += 100;
                ${"yesterday_{$value}_change"} = -${"yesterday_{$value}_change"};
            } elseif(${"yesterday_$value"} - ($the_day_before_yesterday_data?->$value ?? 0) > 0 ) {
                ${"yesterday_{$value}_change"} -= 100;
            }
        }

        return [
            'total_users' => [
                'value' => number_format($yesterday_total_users),
                'diff' => $yesterday_total_users_change
            ],
            'total_sessions' => [
                'value' => number_format($yesterday_total_sessions),
                'diff' => $yesterday_total_sessions_change
            ],
            'total_sessions_duration' => [
                'value' => CarbonInterval::seconds($yesterday_total_sessions_duration)->cascade()->floorMinutes(),
                'diff' => $yesterday_total_sessions_duration_change
            ],
            'total_requests' => [
                'value' => number_format($yesterday_total_requests),
                'diff' => $yesterday_total_requests_change
            ],
            'total_songs' => [
                'value' => number_format($yesterday_total_songs) . ' songs',
                'diff' => $yesterday_total_songs_change,
            ],
            'total_user_upload_songs' => [
                'value' => number_format($yesterday_total_user_upload_songs) . ' songs',
                'diff' => $yesterday_total_user_upload_songs_change,
            ],
            'total_played_time' => [
                'value' => CarbonInterval::seconds($yesterday_total_played_time)->cascade()->floorMinutes(),
                'diff' => $yesterday_total_played_time_change,
            ],
            'total_played_duration' => [
                'value' => CarbonInterval::seconds($yesterday_total_played_duration)->cascade()->floorMinutes(),
                'diff' => $yesterday_total_played_duration_change,
            ],
            'total_albums' => [
                'value' => number_format($yesterday_total_albums) . ' albums',
                'diff' => $yesterday_total_albums_change,
            ],
            'total_artists' => number_format($yesterday_total_artists) . ' artists',
            'total_genres' => number_format($yesterday_total_genres) . ' genres',
            'total_playlists' => number_format($yesterday_total_playlists) . ' playlists',
        ];
    }


    public function calculateRecord($start = null, $end = null): object {
        if (!$start || !$end || $start >= $end) {
            $start = now()->subDays(8)->toDateString();
            $end = now()->addDay()->toDateString();
        }

        $web_statistic = WebStatistic::whereDate('created_at', '>=', Carbon::make($start)->subDays())
            ->whereDate('created_at', '<=', Carbon::make($end)->addDay())
            ->get();

        $initial_data = [
            'total_users', 'total_sessions', 'total_sessions_duration', 'total_requests',
            'total_songs', 'total_user_upload_songs', 'total_played_time', 'total_played_duration',
            'total_albums', 'total_artists', 'total_genres', 'total_playlists'
        ];

        $result = array_reduce($initial_data, function ($carry, $value) {
            $carry[$value] = [
                'labels' => [],
                'name' => ucwords(str_replace('_', ' ', $value)),
                'values' => [],
            ];
            return $carry;
        }, []);

        foreach ($web_statistic as $item) {
            $date = $item->created_at->subDays()->format('d/m/Y');

            foreach ($initial_data as $value) {
                $result[$value]['values'][] = $item->$value;
                $result[$value]['labels'][] = $date;
            }
        }

        return (object) $result;
    }

}
