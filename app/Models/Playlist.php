<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Orchid\Screen\AsSource;

class Playlist extends Model
{
    use HasFactory, AsSource;

    protected $primaryKey = 'playlist_id';
    public $incrementing = false;
    public $timestamps = true;

    public function song()
    {
        return $this->belongsToMany(
            Song::class,
            'playlist_songs',
            'playlist_id',
            'song_id'
        )->withTimestamps();
    }
}
