<?php

namespace App\Http\Controllers\admin;

use App\Enum\RefererEnum;
use App\Models\Song;
use App\Models\Album;
use App\Orchid\Screens\Traits\GenerateQueryStringFilter;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Orchid\Support\Facades\Toast;

trait AlbumAdminController
{
    //
    use GenerateQueryStringFilter;

    public function asyncPassingId(Request $request)
    {
        return [
            'id' => $request->get('id'),
        ];
    }

    public function create(Request $request){
        $request->validate([
            'name' => 'required',
            'release_year' => 'required|numeric|regex:/^[0-9]{4}$/',
            'type' => 'required',
            'cover_source' => 'required',
            'url' => 'required_if:cover_source,url',
            'file' => 'required_if:cover_source,file|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
        $name = $request->name;
        $release_year = $request->release_year;
        $type = $request->type;
        $album = new Album();
        $album->album_id = "album_" . \Str::random(10);
        $album->name = $name;
        $album->release_year = $release_year;
        $album->type = $type;
        $album->user_id = Auth::user()->user_id;
        $album->referer = RefererEnum::ADMIN;
        $album->save();
        if($request->cover_source == 'url'){
            try {
                $album->downloadCoverByUrl($request->url);
            } catch (\Exception $e) {
                Toast::error($e->getMessage());
                return redirect()->back();
            }
        } elseif ($request->cover_source == 'file') {
            try {
                $file = $request->file('file');
                \Storage::disk('final_cover')->put(file_path_helper($album->id) . '/thumbnail.jpg', file_get_contents($file));
                $album->image_path = file_path_helper($album->id) . '/thumbnail.jpg';
                $album->save();
            } catch (\Exception $e) {
                Toast::error($e->getMessage());
                return redirect()->back();
            }
        }

        Toast::success('Album created successfully!');
        $query = $this->generateQueryStringFilter('id', $album->album_id);
        return redirect()->to(route('platform.app.albums'). "?$query");
    }

    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'release_year' => 'required|numeric|regex:/^[0-9]{4}$/',
            'type' => 'required',
            'url' => 'required_if:cover_source,url',
            'file' => 'required_if:cover_source,file|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
        $name = $request->name;
        $release_year = $request->release_year;
        $type = $request->type;
        $album = Album::find($request->id);
        $album->name = $name;
        $album->release_year = $release_year;
        $album->type = $type;
        if($request->cover_source == 'url'){
            try {
                $album->downloadCoverByUrl($request->url);
            } catch (\Exception $e) {
                Toast::error($e->getMessage());
                return redirect()->back();
            }
        } elseif ($request->cover_source == 'file') {
            try {
                $file = $request->file('file');
                \Storage::disk('final_cover')->put(file_path_helper($album->id) . '/thumbnail.jpg', file_get_contents($file));
                $album->image_path = file_path_helper($album->id) . '/thumbnail.jpg';
                $album->save();
            } catch (\Exception $e) {
                Toast::error($e->getMessage());
                return redirect()->back();
            }
        }

        $album->save();
        Toast::success('Album created successfully!');
        $query = $this->generateQueryStringFilter('id', $album->album_id);
        return redirect()->to(route('platform.app.albums'). "?$query");
    }

    public function syncSongs(Request $request) {
        $request->validate([
            'songs' => 'required|array',
            'id' => 'required|exists:albums,album_id'
        ]);
        $album = Album::find($request->id);
        foreach ($album->song as $song) {
            $song->album_id = null;
            $song->save();
        }
        foreach ($request->songs as $song) {
            try {
                $song = Song::findOrFail($song['song_id']);
                $song->album_id = $request->id;
                $song->save();
            } catch (\Exception $e) {
                Toast::error($e->getMessage());
                return redirect()->back();
            }
        }
        Toast::success('Songs synced successfully!');
        $query = $this->generateQueryStringFilter('id',$request->id);
        return redirect()->to(route('platform.app.albums'). "?$query");
    }

    public function delete(Request $request)
    {
        $album = Album::find($request->id);
        if (!$album) {
            Toast::error('Album not found!');
            return redirect()->back();
        }

        $songs = Song::where('album_id', $album->album_id)->get();
        foreach ($songs as $song) {
            $song->album_id = null;
            $song->save();
        }

        \Storage::disk('final_cover')->delete($album->image_path);
        Toast::warning("Album \"{$album->name}\" deleted successfully!");
        $album->delete();
        return redirect()->route('platform.app.albums');
    }
}
