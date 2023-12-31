<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Album extends Model
{
    use HasFactory;
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
}
