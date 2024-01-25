<?php

namespace App\Http\Controllers;

use App\Models\Song;
use App\Models\Album;
use App\Models\Genre;
use App\Models\Artist;
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
        // $top = Genre::where('genre_id', $id)->with([
        //     'song' => fn ($q) => $q->with(['artist', 'album', "like"])->orderBy('listens', 'desc')->limit(100),
        // ])->first();
        $songs = Song::whereHas('genre', fn ($q) => $q->where('genres.genre_id', $id))
            ->with(['artist', 'album', 'like'])
            ->orderBy('listens', 'desc')
            ->limit(10)
            ->get();
        return response()->json($songs, Response::HTTP_OK);
    }

    public function getTopArtistByGenre($id)
    {
        // $top = Genre::where('genre_id', $id)->with([
        //     'artist' => fn ($q) => $q->orderBy('listens', 'desc')->limit(10),
        // ])->first();
        $artists = Artist::whereHas('genres', fn ($q) => $q->where('genres.genre_id', $id))
            ->orderBy('listens', 'desc')
            ->limit(10)
            ->get();
        return response()->json($artists, Response::HTTP_OK);
    }

    public function getTopAlbumByGenre($id)
    {
        // $top = Genre::where('genre_id', $id)->with([
        //     'album' => fn ($q) => $q->withCount(['song' => fn ($query) => $query->where('display', 'public')])
        //         ->having('song_count', '>', 0)
        //         ->withSum('song', 'listens')
        //         ->orderBy('song_sum_listens', 'desc')
        //         ->limit(10)
        //         ->get(),
        // ])->first();
        $albums = Album::whereHas('genre', fn ($q) => $q->where('genres.genre_id', $id))
            ->withCount(['song' => fn ($query) => $query->where('display', 'public')])
            ->having('song_count', '>', 0)
            ->withSum('song', 'listens')
            ->orderBy('song_sum_listens', 'desc')
            ->limit(10)
            ->get();
        return response()->json($albums, Response::HTTP_OK);
    }
}
