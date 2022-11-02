<?php

namespace App\Http\Controllers;

use App\Models\Artist;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

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

    public function getTop()
    {
        $artist =  DB::table('artists')
            ->leftJoin('song_artists', 'artists.artist_id', '=', 'song_artists.artist_id')
            ->leftJoin('songs', 'song_artists.song_id', '=', 'songs.song_id')
            ->select('artists.*', DB::raw('count(songs.song_id) as total'))
            ->limit(8)
            ->get();
        return response()->json([
            'status' => 'success',
            'artists' => $artist
        ]);
    }
}
