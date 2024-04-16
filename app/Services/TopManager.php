<?php

namespace App\Services;

use App\Models\Album;
use App\Models\Artist;

class TopManager
{
    use NeedCachedService;

    protected function getTopAlbums($limit = 8) {
        return Album::with(['user'])
            ->withCount(['song' => fn ($query) => $query->where('display', 'public')])
            ->having('song_count', '>', 0)
            ->take($limit)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    protected function getTopArtists($limit = 8) {
        return Artist::orderBy("listens", "desc")
            ->limit($limit)
            ->get();
    }
}
