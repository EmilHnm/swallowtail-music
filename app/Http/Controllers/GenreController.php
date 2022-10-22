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
        $listArtist = Genre::all();
        return response()->json($listArtist, Response::HTTP_OK);
    }

    public function show($id)
    {
        $artist = Genre::where('genre_id', $id)->first();
        return response()->json($artist, Response::HTTP_OK);
    }
}
