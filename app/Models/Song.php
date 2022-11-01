<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Song extends Model
{
    use HasFactory;

    protected $primaryKey = 'song_id';
    public $incrementing = false;
    public $timestamps = true;

    public function genre()
    {
        return $this->belongsToMany(
            Genre::class,
            'song_genres',
            'song_id',
            'genre_id'
        )->withTimestamps();
    }

    public function artist()
    {
        return $this->belongsToMany(
            Artist::class,
            'song_artists',
            'song_id',
            'artist_id'
        )->withTimestamps();
    }
}
