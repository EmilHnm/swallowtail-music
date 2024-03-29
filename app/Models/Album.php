<?php

namespace App\Models;

use App\Enum\SongMetadataStatusEnum;
use App\Models\Traits\AdvancedFilters;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;
use Orchid\Screen\AsSource;

class Album extends Model
{
    use HasFactory;
    use AsSource, AdvancedFilters;
    use Searchable;
    protected $primaryKey = "album_id";
    public $incrementing = false;
    public $timestamps = true;

    public function user()
    {
        return $this->belongsTo(User::class, "user_id", "user_id");
    }

    public function song()
    {
        return $this->hasMany(Song::class, "album_id", "album_id");
    }

    public function genre()
    {
        return $this->belongsToMany(Genre::class, AlbumGenre::class, "album_id", "genre_id")->withTimestamps();
    }

    public function downloadCoverByUrl($url)
    {
        $contents = file_get_contents($url);
        \Storage::disk('final_cover')->put($this->id . "/thumbnail.jpg", $contents);
        $this->image_path = $this->id . "/thumbnail.jpg";
        $this->save();
    }

    public function toSearchableArray()
    {
        $with = ['song'];
        $this->loadMissing($with);
        return $this->toArray();
    }
}
