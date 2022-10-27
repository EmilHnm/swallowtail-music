<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Song;
use App\Models\Album;
use App\Models\SongAlbum;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use ProtoneMedia\LaravelFFMpeg\Support\FFMpeg;
use ProtoneMedia\LaravelFFMpeg\Exporters\EncodingException;

class AlbumController extends Controller
{
    //

    public function uploadAlbum(Request $request)
    {
        $album_id = "album_" . Str::random(8) . Carbon::now()->timestamp;
        $name = $request->albumTitle;
        $release_year = $request->albumReleaseYear;
        $type = $request->albumType;
        $songCount = json_decode($request->songCount);
        $album = new Album();
        if ($request->file('albumImage')) {
            $imageFile = $request->file('albumImage');
            $fileName = $album_id . '.' . $imageFile->getClientOriginalExtension();
            $imageFile->move(public_path('storage/upload/album_cover'), $fileName);
            $album->image_path = $fileName;
        }

        $album->name = $name;
        $album->album_id = $album_id;
        $album->release_year = $release_year;
        $album->type = $type;
        echo json_encode([
            'status' => 'success',
            'message' => 'Album uploaded successfully!
            All songs uploaded will be ready in some minutes and saved as private by default when finish.
            Please go to Account/Upload Management/Song to update song infomation.'
        ]);
        for ($i = 0; $i < $songCount; $i++) {
            $song = new Song();
            $song->song_id = 'song_' . date('YmdHi') . Str::random(10);
            $song->user_id = Auth::user()->user_id;
            $song->title = $request->songName . '_' . $i;
            $song->display = 'private';
            $song->listens = 0;
            if ($request->file('songFile_' . $i)) {
                try {
                    $songFile = $request->file('songFile_' . $i);
                    $tempFileName = date('YmdHi') . $songFile->getClientOriginalName();
                    $songFile->move(public_path('storage/upload/song_src'), $tempFileName);
                    FFMpeg::fromDisk('final_audio')
                        ->open($tempFileName)
                        ->export()
                        ->addFilter(["-strict", "-2", "-acodec", "vorbis", '-b:a', '320k'])
                        ->save($song->song_id . '.ogg');
                    @unlink(public_path('storage/upload/song_src/') . $tempFileName);
                    $duration = FFMpeg::fromDisk('final_audio')
                        ->open($song->song_id . '.ogg')
                        ->getDurationInSeconds();
                    $song->duration = $duration;
                    FFMpeg::cleanupTemporaryFiles();
                } catch (EncodingException $exception) {
                    $command = $exception->getCommand();
                    $errorLog = $exception->getErrorOutput();
                    dd($command, $errorLog);
                }
            }
            $song_album = new SongAlbum();
            $song_album->song_id = $song->song_id;
            $song_album->album_id = $album->album_id;
            $song->save();
            $song_album->save();
        }

        $album->save();
    }
}
