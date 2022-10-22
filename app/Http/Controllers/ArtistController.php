<?php

namespace App\Http\Controllers;

use App\Models\Artist;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ArtistController extends Controller
{
    //

    public function getAll()
    {
        $listArtist = Artist::all();
        return response()->json($listArtist, Response::HTTP_OK);
    }

    public function show($id)
    {
        $artist = Artist::where('artist_id', $id)->first();
        return response()->json($artist, Response::HTTP_OK);
    }
}
