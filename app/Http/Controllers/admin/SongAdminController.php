<?php

namespace App\Http\Controllers\admin;

use App\Models\Song;
use App\Models\LikedSong;
use App\Orchid\Helpers\Text;
use Illuminate\Http\Request;
use App\Services\SongManager;
use Orchid\Support\Facades\Toast;

trait SongAdminController
{
    //

    public function asyncPassingId(Request $request)
    {
        return [
            'id' => $request->get('id'),
        ];
    }

    public function update(Request $request)
    {

        $request->validate([
            'title' => 'required',
            'song_id' => 'required',
            'display' => 'required',
            'artist_id' => 'required_if:display,public|array',
            'genre_id' => 'required_if:display,public|array',
        ]);
        $artists = $request->artist_id;
        $genres =$request->genre_id;
        $song = Song::find($request->song_id);
        $song->title = $request->title;
        $song->normalized_title = Text::normalize($song->title);
        $song->display = $request->display;
        $song->album_id = $request->album_id;
        $song->save();
        $song->artist()->sync($artists);
        $song->genre()->sync($genres);
        Toast::success('Album created successfully!');
        $query = $this->generateQueryStringFilter('id', $song->song_id);
        return redirect()->to(route('platform.app.songs'). "?$query");
    }

    public function delete(Request $request)
    {
        $song = Song::find($request->id);
        if (!$song) {
            Toast::error('Song not found!');
            return redirect()->back();
        } else {
            $song->artist()->detach();
            $song->genre()->detach();
            $song->playlist()->detach();
            LikedSong::where("song_id", $request->id)->delete();
            $songManager = new SongManager($song);
            $songManager->removeFile();
            $song->delete();
            Toast::warning('Song not found!');
            return redirect()->route('platform.app.songs');
        }
    }

    public function reindex($id) {
        $song = Song::find($id);
        try {
            if ($song) {
                $song->searchable();
                Toast::success('Song re-indexed successfully!');
            } else {
                Toast::error('Song not found!');
            }
        } catch (\Throwable $th) {
            Toast::error($th->getMessage());
        }
        return redirect()->back();
    }
}
