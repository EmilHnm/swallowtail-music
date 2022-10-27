<?php

namespace App\Http\Controllers;

use App\Models\Song;
use App\Models\Genre;
use App\Models\Artist;
use App\Models\SongGenre;
use App\Models\SongArtist;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use FFMpeg\Format\Audio\Vorbis;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use ProtoneMedia\LaravelFFMpeg\FFMpeg\FFProbe;
use ProtoneMedia\LaravelFFMpeg\Support\FFMpeg;
use ProtoneMedia\LaravelFFMpeg\Exporters\EncodingException;

class SongController extends Controller
{
    //

    public function uploadSong(Request $request)
    {
        $artist = json_decode($request->artist);
        $newArtist = json_decode($request->newArtist);
        $genre = json_decode($request->genre);
        $newGenre = json_decode($request->newGenre);

        $song = new Song();
        $song->song_id = 'song_' . date('YmdHi') . Str::random(10);
        $song->user_id = Auth::user()->user_id;
        $song->title = $request->songName;
        $song->display = $request->displayMode;
        $song->listens = 0;

        foreach ($artist as $id) {
            $song_artist = new SongArtist();
            $song_artist->song_id = $song->song_id;
            $song_artist->artist_id = $id;
            $song_artist->save();
        }

        foreach ($newArtist as $name) {
            $artist = new Artist();
            $artist->artist_id = 'artist_' . Str::random(10);
            $artist->name = $name;
            $artist->image_path = "";
            $artist->save();
            $song_artist = new SongArtist();
            $song_artist->artist_id = $artist->artist_id;
            $song_artist->song_id = $song->song_id;
            $song_artist->save();
        }

        foreach ($genre as $id) {
            $song_genre = new SongGenre();
            $song_genre->song_id = $song->song_id;
            $song_genre->genre_id = $id;
            $song_genre->save();
        }

        foreach ($newGenre as $name) {
            $genre = new Genre();
            $genre->genre_id = 'genre_' . Str::random(10);
            $genre->name = $name;
            $genre->save();
            $song_genre = new SongGenre();
            $song_genre->genre_id = $genre->genre_id;
            $song_genre->song_id = $song->song_id;
            $song_genre->save();
        }

        if ($request->file('songFile')) {
            try {
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Song uploaded successfully. It will ready in some minutes.'
                ]);
                // dd($request->file('songFile'));
                $file = $request->file('songFile');
                $filename = date('YmdHi') . $file->getClientOriginalName();
                $file->move(public_path('storage/upload/song_src'), $filename);
                FFMpeg::fromDisk('final_audio')
                    ->open($filename)
                    ->export()
                    ->addFilter(["-strict", "-2", "-acodec", "vorbis", '-b:a', '320k'])
                    ->save($song->song_id . '.ogg');
                @unlink(public_path('storage/upload/song_src/') . $filename);
                $duration = FFMpeg::fromDisk('final_audio')
                    ->open($song->song_id . '.ogg')
                    ->getDurationInSeconds();
                $song->duration = $duration;
                $song->save();
                FFMpeg::cleanupTemporaryFiles();
            } catch (EncodingException $exception) {
                $command = $exception->getCommand();
                $errorLog = $exception->getErrorOutput();
                dd($command, $errorLog);
            }
        }
    }

    public function uploadedSong()
    {
        $songsData = DB::select(
            'SELECT songs.*,  artists.name AS artist_name,artists.artist_id AS artist_id
                        FROM songs
                        JOIN song_artists ON songs.song_id = song_artists.song_id
                        JOIN artists ON song_artists.artist_id =artists.artist_id
                        WHERE songs.user_id= ?',
            [Auth::user()->user_id]
        );

        return response()->json([
            'status' => 'success',
            'songs' => $songsData
        ]);
    }

    public function getSongInfo($id)
    {
        $song = Song::where('song_id', $id)->first();
        $genre = $song->genre()->where('song_id', $id)->get();
        $artist = $song->artist()->where('song_id', $id)->get();
        return response()->json([
            'status' => 'success',
            'song' => $song,
            'genre' => $genre,
            'artist' => $artist
        ]);
    }
}
