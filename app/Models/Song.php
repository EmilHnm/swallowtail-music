<?php

namespace App\Models;

use App\Enum\SongMetadataStatusEnum;
use App\Models\Traits\AdvancedFilters;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Scout\Searchable;
use Orchid\Screen\AsSource;

class Song extends Model
{
    use HasFactory;
    use AsSource, AdvancedFilters;
    use Searchable;

    protected $primaryKey = "song_id";
    public $incrementing = false;
    public $timestamps = true;

    protected static function booted()
    {
        static::addGlobalScope('duration', fn ($query) => $query->with(["file" =>  fn($q) => $q->select(["song_id","duration"])]));
        static::addGlobalScope('playable', fn ($query) => $query->whereHas("file", fn ($q) => $q->where("status", SongMetadataStatusEnum::PUBLISH)));
    }

    public function genre()
    {
        return $this->belongsToMany(
            Genre::class,
            SongGenre::class,
            "song_id",
            "genre_id"
        )->withTimestamps();
    }

    public function artist()
    {
        return $this->belongsToMany(
            Artist::class,
            "song_artists",
            "song_id",
            "artist_id"
        )->withTimestamps();
    }

    public function playlist()
    {
        return $this->belongsToMany(
            Playlist::class,
            "playlist_songs",
            "song_id",
            "playlist_id"
        )->withTimestamps();
    }

    public function user()
    {
        return $this->belongsTo(User::class, "user_id", "user_id");
    }
    public function album()
    {
        return $this->belongsTo(Album::class, "album_id", "album_id");
    }

    public function like()
    {
        return $this->hasMany(LikedSong::class, "song_id", "song_id")->where(
            "user_id",
            Auth::user()->user_id
        );
    }

    public function file()
    {
        return $this->hasOne(SongMetadata::class, "song_id", "song_id");
    }


    public function toSearchableArray()
    {
        $with = ['artist', 'genre', 'album'];
        $this->loadMissing($with)
            ->whereHas("file", fn ($q) => $q->where("status", SongMetadataStatusEnum::PUBLISH));
        return $this->toArray();
    }

    public function getRouteKeyName()
    {
        return "song_id";
    }

    public function isPublishable() : bool
    {
        return $this->artist()->exists() && $this->genre()->exists() && $this->file()->where('status', SongMetadataStatusEnum::PUBLISH)->exists();
    }
}
