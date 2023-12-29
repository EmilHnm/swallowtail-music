<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SongMetadata extends Model
{
    use HasFactory;

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
