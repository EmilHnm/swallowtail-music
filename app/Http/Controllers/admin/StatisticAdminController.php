<?php

namespace App\Http\Controllers\admin;

use App\Models\WebStatistic;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Illuminate\Support\Facades\Redis;

trait StatisticAdminController
{
    protected function getCurrentData() : array
    {
        $current_total_played_time = Redis::get(Carbon::today()->timestamp . '_total_played_time') ?? 0;
        $current_total_played_duration = Redis::get(Carbon::today()->timestamp . '_total_played_duration') ?? 0;
        $current_total_requests = Redis::get(Carbon::today()->timestamp . '_total_played_time') ?? 0;
        $current_total_sessions_duration = Redis::get(Carbon::today()->timestamp . '_total_sessions_duration') ?? 0;

        return [
            'total_played_time'    => ['value' => number_format(+$current_total_played_time) . ' times'],
            'total_played_duration' => ['value' => CarbonInterval::seconds(+$current_total_played_duration)->cascade()->floorMinutes()],
            'total_requests'    => number_format(+$current_total_requests) . ' requests',
            'total_sessions_duration'   => ['value' => CarbonInterval::seconds(+$current_total_sessions_duration)->cascade()->floorMinutes()],
        ];
    }

    protected function getYesterdayData() : array
    {
        $yesterday_data = WebStatistic::whereDate('created_at', Carbon::today())->first();
        if ($yesterday_data) {
            $yesterday_total_users = $yesterday_data->total_users;
            $yesterday_total_sessions = $yesterday_data->total_sessions;
            $yesterday_total_sessions_duration = $yesterday_data->total_sessions_duration;
            $yesterday_total_requests = $yesterday_data->total_requests;

            $yesterday_total_songs = $yesterday_data->total_songs;
            $yesterday_total_user_upload_songs = $yesterday_data->total_user_upload_songs;
            $yesterday_total_played_time = $yesterday_data->total_played_time;
            $yesterday_total_played_duration = $yesterday_data->total_played_duration;

            $yesterday_total_albums = $yesterday_data->total_albums;
            $yesterday_total_artists = $yesterday_data->total_artists;
            $yesterday_total_genres = $yesterday_data->total_genres;
            $yesterday_total_playlists = $yesterday_data->total_playlists;
        } else {
            $yesterday_total_users = 0;
            $yesterday_total_sessions = 0;
            $yesterday_total_sessions_duration = 0;
            $yesterday_total_requests = 0;

            $yesterday_total_songs = 0;
            $yesterday_total_user_upload_songs = 0;
            $yesterday_total_played_time = 0;
            $yesterday_total_played_duration = 0;

            $yesterday_total_albums = 0;
            $yesterday_total_artists = 0;
            $yesterday_total_genres = 0;
            $yesterday_total_playlists = 0;
        }

        $the_day_before = Carbon::yesterday()->subDay();
        $the_day_before_data = WebStatistic::whereDate('created_at', $the_day_before)->first();
        if ($the_day_before_data) {

            $yesterday_total_users_change = ($yesterday_total_users / $the_day_before_data->total_users) * 100 - 1;
            $yesterday_total_sessions_change = ($yesterday_total_sessions / $the_day_before_data->total_sessions) * 100 - 1;
            $yesterday_total_sessions_duration_change = ($yesterday_total_sessions_duration / $the_day_before_data->total_sessions_duration) * 100 - 1;
            $yesterday_total_requests_change = ($yesterday_total_requests / $the_day_before_data->total_requests) * 100 - 1;

            $yesterday_total_songs_change = ($yesterday_total_songs / $the_day_before_data->total_songs) * 100 - 1;
            $yesterday_total_user_upload_songs_change = ($yesterday_total_user_upload_songs / $the_day_before_data->total_user_upload_songs) * 100 - 1;
            $yesterday_total_played_time_change = ($yesterday_total_played_time / $the_day_before_data->total_played_time) * 100 - 1;
            $yesterday_total_played_duration_change = ($yesterday_total_played_duration / $the_day_before_data->total_played_duration) * 100 - 1;

            $yesterday_total_albums_change = ($yesterday_total_albums / $the_day_before_data->total_albums) * 100 - 1;
            $yesterday_total_artists_change = ($yesterday_total_artists / $the_day_before_data->total_artists) * 100 - 1;
            $yesterday_total_genres_change = ($yesterday_total_genres / $the_day_before_data->total_genres) * 100 - 1;
            $yesterday_total_playlists_change = ($yesterday_total_playlists / $the_day_before_data->total_playlists) * 100 - 1;
        } else {
            $yesterday_total_users_change = 0;
            $yesterday_total_sessions_change = 0;
            $yesterday_total_sessions_duration_change = 0;
            $yesterday_total_requests_change = 0;

            $yesterday_total_songs_change = 0;
            $yesterday_total_user_upload_songs_change = 0;
            $yesterday_total_played_time_change = 0;
            $yesterday_total_played_duration_change = 0;

            $yesterday_total_albums_change = 0;
            $yesterday_total_artists_change = 0;
            $yesterday_total_genres_change = 0;
            $yesterday_total_playlists_change = 0;
        }

        return [
            'total_users' =>[
                'value' => number_format(+$yesterday_total_users),
                'diff' => $yesterday_total_users_change
            ],
            'total_sessions' => [
                'value' => number_format(+$yesterday_total_sessions),
                'diff' => $yesterday_total_sessions_change
            ],
            'total_sessions_duration' => [
                'value' => CarbonInterval::seconds($yesterday_total_sessions_duration)->cascade()->floorMinutes(),
                'diff' => $yesterday_total_sessions_duration_change
            ],
            'total_requests' => [
                'value' => number_format(+$yesterday_total_requests),
                'diff' => $yesterday_total_requests_change
            ],
            'total_songs' => [
                'value' => number_format(+$yesterday_total_songs) . ' songs',
                'diff' => $yesterday_total_songs_change,
            ],
            'total_user_upload_songs' => [
                'value' => number_format(+$yesterday_total_user_upload_songs) . ' songs',
                'diff' => $yesterday_total_user_upload_songs_change,
            ],
            'total_played_time' => [
                'value' => CarbonInterval::seconds(+$yesterday_total_played_time)->cascade()->floorMinutes(),
                'diff' => $yesterday_total_played_time_change,
            ],
            'total_played_duration' => [
                'value' => CarbonInterval::seconds(+$yesterday_total_played_duration)->cascade()->floorMinutes(),
                'diff' => $yesterday_total_played_duration_change,
            ],
            'total_albums' => [
                'value' => number_format(+$yesterday_total_albums) . ' albums',
                'diff' => $yesterday_total_albums_change,
            ],
            'total_artists' => number_format(+$yesterday_total_artists) . ' artists',
            'total_genres' => number_format(+$yesterday_total_genres) . ' genres',
            'total_playlists' => number_format(+$yesterday_total_playlists) . ' playlists',
        ];
    }

    public function calculateRecord($start = null, $end = null) : object {
        if (!$start || !$end) {
            $web_statistic = WebStatistic::where('created_at', '>=', now()->subDays(8)->toDateString())
                ->get();

        } elseif ($start >= $end) {
            $start = now()->subDays(8)->toDateString();
            $end = now()->addDays()->toDateString();
            $web_statistic = WebStatistic::whereDate('created_at', '>=', $start)
                ->whereDate('created_at', '<=', $end)
                ->get();
        } else {
            if (Carbon::now() < Carbon::make($end)->addDays()) $end = Carbon::now()->addDay()->toDateString();
            $web_statistic = WebStatistic::whereDate('created_at', '>=' ,Carbon::make($start)->subDays()->toDateString())
                ->whereDate('created_at', '<=' ,Carbon::make($end)->addDays()->toDateString())
                ->get();

        }
        return $web_statistic->reduce(function (object $carry, WebStatistic $item) {
            $date = $item->created_at->subDays()->format('d/m/Y');

            $carry->total_users['values'][] = $item->total_users;
            $carry->total_users['labels'][] = $date;

            $carry->total_sessions['values'][] = $item->total_sessions;
            $carry->total_sessions['labels'][] = $date;

            $carry->total_sessions_duration['values'][] = $item->total_sessions_duration;
            $carry->total_sessions_duration['labels'][] = $date;

            $carry->total_requests['values'][] = $item->total_requests;
            $carry->total_requests['labels'][] = $date;

            $carry->total_songs['values'][] = $item->total_songs;
            $carry->total_songs['labels'][] = $date;

            $carry->total_user_upload_songs['values'][] = $item->total_user_upload_songs;
            $carry->total_user_upload_songs['labels'][] = $date;

            $carry->total_played_time['values'][] = $item->total_played_time;
            $carry->total_played_time['labels'][] = $date;

            $carry->total_played_duration['values'][] = $item->total_played_duration;
            $carry->total_played_duration['labels'][] = $date;

            $carry->total_albums['values'][] = $item->total_albums;
            $carry->total_albums['labels'][] = $date;

            $carry->total_artists['values'][] = $item->total_artists;
            $carry->total_artists['labels'][] = $date;

            $carry->total_genres['values'][] = $item->total_genres;
            $carry->total_genres['labels'][] = $date;

            $carry->total_playlists['values'][] = $item->total_playlists;
            $carry->total_playlists['labels'][] = $date;

            return $carry;
        }, (object)[
            'total_users' => [
                'labels' => [],
                'name' => 'Total Users',
                'values' => [],
            ],
            'total_sessions' => [
                'labels' => [],
                'name' => 'Total Sessions',
                'values' => [],
            ],
            'total_sessions_duration' => [
                'labels' => [],
                'name' => 'Total Sessions Duration',
                'values' => [],
            ],
            'total_requests' => [
                'labels' => [],
                'name' => 'Total Requests',
                'values' => [],
            ],

            'total_songs' => [
                'labels' => [],
                'name' => 'Total Songs',
                'values' => [],
            ],
            'total_user_upload_songs' => [
                'labels' => [],
                'name' => 'Total User Upload Songs',
                'values' => [],
            ],
            'total_played_time' => [
                'labels' => [],
                'name' => 'Total Played Time',
                'values' => [],
            ],
            'total_played_duration' => [
                'labels' => [],
                'name' => 'Total Played Duration',
                'values' => [],
            ],

            'total_albums' => [
                'labels' => [],
                'name' => 'Total Albums',
                'values' => [],
            ],
            'total_artists' => [
                'labels' => [],
                'name' => 'Total Artists',
                'values' => [],
            ],
            'total_genres' => [
                'labels' => [],
                'name' => 'Total Genres',
                'values' => [],
            ],
            'total_playlists' => [
                'labels' => [],
                'name' => 'Total Playlists',
                'values' => [],
            ],
        ]);
    }
}
