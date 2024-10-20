<?php

namespace App\Models;

use App\Models\Traits\AdvancedFilters;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Orchid\Screen\AsSource;

class Genre extends Model
{
    use HasFactory;
    use AsSource, AdvancedFilters;
    protected $primaryKey = "genre_id";
    public $incrementing = false;
    public $timestamps = true;

    public function song()
    {
        return $this->belongsToMany(
            Song::class,
            "song_genres",
            "genre_id",
            "song_id"
        )->withTimestamps();
    }

    public function artist()
    {
        return $this->belongsToMany(
            Artist::class,
            "artist_genres",
            "genre_id",
            "artist_id"
        )->withTimestamps();
    }

    public function album()
    {
        return $this->belongsToMany(
            Album::class,
            AlbumGenre::class,
            "genre_id",
            "album_id"
        )->withTimestamps();
    }
}
