<?php

namespace App\Models;

use App\Models\Traits\AdvancedFilters;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Orchid\Screen\AsSource;

class SongMetadata extends Model
{
    use HasFactory;
    use AsSource, AdvancedFilters;

    public function song()
    {
        return $this->belongsTo(Song::class, 'song_id');
    }

    public function getLyricData()
    {
        if (!$this->lyrics) {
            return null;
        }
        $lyrics = json_decode($this->lyrics)->lyric ?? [];
        if (count($lyrics) > 1)
            return $lyrics;
        elseif (count($lyrics) == 1)
            return explode("\n", $lyrics[0]);
        return  $lyrics;
    }
}
