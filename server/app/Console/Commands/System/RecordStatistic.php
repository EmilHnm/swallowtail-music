<?php

namespace App\Console\Commands\System;

use App\Enum\RefererEnum;
use App\Models\WebStatistic;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;
use Laravel\Sanctum\PersonalAccessToken;

class RecordStatistic extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:record-statistic';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to record system statistics.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
        $this->info('Recording system statistics...');
        //
        $totalUsers = \App\Models\User::count();
        $totalSessions = PersonalAccessToken::where('last_used_at', '<=', Carbon::yesterday())->count();
        $totalSessionsDuration = Redis::get(Carbon::yesterday()->timestamp . '_total_sessions_duration') ?? 0;
        $totalRequests = Redis::get(Carbon::yesterday()->timestamp . '_total_requests') ?? 0;
        //
        $totalSong = \App\Models\Song::count();
        $totalUserUploadSongs = \App\Models\Song::whereHas('file', function($query) {
            $query->where('referer', RefererEnum::USER);
        })->count();
        $totalPlayedTime = Redis::get(Carbon::yesterday()->timestamp . '_total_played_time') ?? 0;
        $totalPlayedDuration = Redis::get(Carbon::yesterday()->timestamp . '_total_played_duration') ?? 0;
        //
        $totalAlbums = \App\Models\Album::count();
        $totalArtists = \App\Models\Artist::count();
        $totalPlaylists = \App\Models\Playlist::count();
        $totalGenres = \App\Models\Genre::count();
        //
        if($statistics = WebStatistic::whereDate('created_at', Carbon::today())->first())
        {
            $statistics->update([
                'total_users' => $totalUsers,
                'total_sessions' => $totalSessions,
                'total_sessions_duration' => $totalSessionsDuration,
                'total_requests' => $totalRequests,
                'total_songs' => $totalSong,
                'total_user_upload_songs' => $totalUserUploadSongs,
                'total_played_time' => $totalPlayedTime,
                'total_played_duration' => $totalPlayedDuration,
                'total_albums' => $totalAlbums,
                'total_artists' => $totalArtists,
                'total_playlists' => $totalPlaylists,
                'total_genres' => $totalGenres,
            ]);
        } else {
            WebStatistic::create([
                'total_users' => $totalUsers,
                'total_sessions' => $totalSessions,
                'total_sessions_duration' => $totalSessionsDuration,
                'total_requests' => $totalRequests,
                'total_songs' => $totalSong,
                'total_user_upload_songs' => $totalUserUploadSongs,
                'total_played_time' => $totalPlayedTime,
                'total_played_duration' => $totalPlayedDuration,
                'total_albums' => $totalAlbums,
                'total_artists' => $totalArtists,
                'total_playlists' => $totalPlaylists,
                'total_genres' => $totalGenres,
            ]);
        }

        // Clear Redis keys
        Redis::del(Carbon::yesterday()->timestamp . '_total_sessions_duration');
        Redis::del(Carbon::yesterday()->timestamp . '_total_requests');
        Redis::del(Carbon::yesterday()->timestamp . '_total_played_time');
        Redis::del(Carbon::yesterday()->timestamp . '_total_played_duration');
        //
        // Log the event
        \Log::info('System statistics recorded at ' . Carbon::now()->toDateTimeLocalString());
        $this->info('System statistics recorded.');
    }
}
