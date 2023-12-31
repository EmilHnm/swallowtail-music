<?php

namespace App\Http\Controllers;

use App\Models\Genre;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class GenreController extends Controller
{
    //
    public function getAll()
    {
        $genres = Genre::all();
        return response()->json($genres, Response::HTTP_OK);
    }

    public function show($id)
    {
        $genre = Genre::where('genre_id', $id)->first();
        return response()->json($genre, Response::HTTP_OK);
    }

    public function getTopSongByGenre($id)
    {
        $top = Genre::where('genre_id', $id)->with([
            'song' => fn ($q) => $q->with(['artist', 'album', "like"])->orderBy('listens', 'desc')->limit(10)
        ])->first();
        return response()->json($top->song, Response::HTTP_OK);
    }

    public function getTopArtistByGenre($id)
    {
        $top = Genre::where('genre_id', $id)->with([
            'artist' => fn ($q) => $q->orderBy('listens', 'desc')->limit(10),
        ])->first();
        return response()->json($top->artist, Response::HTTP_OK);
    }

    public function getTopAlbumByGenre($id)
    {
        $top = Genre::where('genre_id', $id)->with([
            'album' => fn ($q) => $q->withCount(['song' => fn ($query) => $query->where('display', 'public')])
                ->having('song_count', '>', 0)
                ->withSum('song', 'listens')
                ->orderBy('song_sum_listens', 'desc')
                ->limit(10)
                ->get(),
        ])->first();
        return response()->json($top->album, Response::HTTP_OK);
    }
}
