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

    public function getSongByGenre($id)
    {
        $genre = Genre::where('genre_id', $id)->first();
        $songs = $genre->songs;
        return response()->json([
            'status' => 'success',
            'song' => $songs
        ], Response::HTTP_OK);
    }
}
