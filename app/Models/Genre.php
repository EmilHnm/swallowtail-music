<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Genre extends Model
{
    use HasFactory;

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
}
