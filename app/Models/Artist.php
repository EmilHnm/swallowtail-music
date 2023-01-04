<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Artist extends Model
{
    use HasFactory;
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
        );
    }
}
