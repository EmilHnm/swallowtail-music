<?php

namespace App\Models;

use App\Models\Traits\AdvancedFilters;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Orchid\Screen\AsSource;

class Artist extends Model
{
    use HasFactory;
    use AsSource, AdvancedFilters;
    protected $primaryKey = "artist_id";
    public $incrementing = false;
    public $timestamps = true;
    public function song()
    {
        return $this->belongsToMany(
            Song::class,
            "song_artists",
            "artist_id",
            "song_id"
        )->withTimestamps();
    }

    public function genres()
    {
        return $this->belongsToMany(
            Genre::class,
            "artist_genres",
            "artist_id",
            "genre_id"
        )->withTimestamps();
    }


    public function downloadImageByUrl($url, $type = 'avatar')
    {
        $contents = file_get_contents($url);
        \Storage::disk('final_artist_image')->put($this->id . "/$type.jpg", $contents);
        if ($type == 'avatar')
            $this->image_path = $this->id . "/$type.jpg";
        elseif($type == 'banner')
            $this->banner_path = $this->id . "/$type.jpg";
        $this->save();
    }
}
