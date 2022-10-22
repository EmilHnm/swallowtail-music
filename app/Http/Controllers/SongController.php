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
                return response()->json([
                    'status' => 'success',
                    'message' => 'Song uploaded successfully'
                ]);
            } catch (EncodingException $exception) {
                $command = $exception->getCommand();
                $errorLog = $exception->getErrorOutput();
                dd($command, $errorLog);
            }
        }
    }
}
