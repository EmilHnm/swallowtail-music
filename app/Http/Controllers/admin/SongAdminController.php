<?php

namespace App\Http\Controllers\admin;

use App\Models\Song;
use App\Models\Genre;
use App\Models\Artist;
use App\Models\LikedSong;
use App\Models\SongGenre;
use App\Models\SongArtist;
use Illuminate\Support\Str;
use App\Models\PlaylistSong;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class SongAdminController extends Controller
{
    //

    public function getAll()
    {
        if (Auth::user()->role === '') {
            return response()->json(['message' => 'You are not authorized to access this resource.'], 403);
        } else {
            $songs = Song::with(["user", "album"])->get();
            return response()->json([
                'songs' => $songs,
                'status' => "success"
            ], 200);
        }
    }

    public function show($id)
    {
        if (Auth::user()->role === '') {
            return response()->json(
                [
                    'status' => 'error',
                    'message' => 'You are not authorized to access this resource.'
                ],
                403
            );
        }
        $song = Song::find($id);
        if (!$song) {
            return response()->json([
                'message' => 'Song not found',
                'status' => 'error'
            ], 404);
        }
        $song = Song::where('song_id', $id)->first();
        if (!$song->id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Song not found'
            ]);
        }
        $genre = $song->genre()->where('song_id', $id)->get();
        $artist = $song->artist()->where('song_id', $id)->get();
        return response()->json([
            'status' => 'success',
            'song' => $song,
            'genre' => $genre,
            'artist' => $artist
        ]);
    }
    public function update(Request $request)
    {
        if (Auth::user()->role == '') {
            echo Auth::user()->role;
            return response()->json(
                [
                    'status' => 'error',
                    'message' => 'You are not authorized to do this action.'
                ],
                403
            );
        }
        $artist = json_decode($request->artist);
        $newArtist = json_decode($request->newArtist);
        $genre = json_decode($request->genre);
        $newGenre = json_decode($request->newGenre);
        $song = Song::find($request->song_id);

        $song->title = $request->songName;
        $song->sub_title = $request->songSubName;
        $song->display = $request->displayMode;
        $song->save();
        $song->artist()->detach();
        $song->genre()->detach();
        $song->artist()->attach($artist);
        $song->genre()->attach($genre);
        foreach ($newArtist as $name) {
            $saveNewArtist = new Artist();
            $saveNewArtist->artist_id = 'artist_' . Str::random(10);
            $saveNewArtist->name = $name;
            $saveNewArtist->image_path = "";
            $saveNewArtist->save();
            $song->artist()->attach([$saveNewArtist->artist_id]);
        }
        foreach ($newGenre as $name) {
            $savenewGenre = new Genre();
            $savenewGenre->genre_id = 'genre_' . Str::random(10);
            $savenewGenre->name = $name;
            $savenewGenre->save();
            $song->genre()->attach([$genre->genre_id]);
        }
        return response()->json([
            'status' => 'success',
            'message' => 'Song Updated Successfully'
        ]);
    }

    public function delete($id)
    {
        if (Auth::user()->role === '') {
            return response()->json(
                [
                    'status' => 'error',
                    'message' => 'You are not authorized to access this resource.'
                ],
                403
            );
        }
        $song = Song::find($id);
        if (!$song) {
            return response()->json([
                'message' => 'Song not found',
                'status' => 'error'
            ], 404);
        } else {
            SongArtist::where('song_id', $id)->delete();
            SongGenre::where('song_id', $id)->delete();
            PlaylistSong::where('song_id', $id)->delete();
            LikedSong::where('song_id', $id)->delete();
            @unlink(public_path('storage/upload/song_src/') . $id . '.ogg');
            $song->delete();
            return response()->json([
                'message' => 'Song deleted successfully',
                'status' => 'success'
            ], 200);
        }
    }
}
