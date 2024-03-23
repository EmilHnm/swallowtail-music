<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WebStatistic extends Model
{
    use HasFactory;

    protected $fillable = [
        'total_users',
        'total_sessions',
        'total_sessions_duration',
        'total_requests',
        'total_songs',
        'total_user_upload_songs',
        'total_played_time',
        'total_played_duration',
        'total_albums',
        'total_artists',
        'total_playlists',
        'total_genres',
    ];
}
