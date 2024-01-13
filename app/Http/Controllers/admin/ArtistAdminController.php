<?php

namespace App\Http\Controllers\admin;

use App\Models\Artist;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Orchid\Support\Facades\Toast;

trait ArtistAdminController
{

    public function asyncPassingId(Request $request)
    {
        return [
            'id' => $request->get('id'),
        ];
    }

    public function create(Request $request)
    {
        $name = $request->get('name');
        $description = $request->get('description');
        $artist = new Artist();
        $artist->artist_id = "artist_" . Str::random(10);
        $artist->name = $name;
        $artist->description = $description;
        $artist->save();
        Toast::info('Artist created');
        return redirect()->route('platform.app.artists');
    }


    public function updateInformation(Request $request) {
        $id = $request->get('id');
        $name = $request->get('name');
        $description = $request->get('description');

        try {
            $artist = Artist::find($id);
            $artist->name = $name;
            $artist->description = $description;
            $artist->save();
        } catch (\Exception $e) {
            Toast::error($e->getMessage());
            return redirect()->back();
        }

        Toast::info('Information saved');
        return redirect()->route('platform.app.artists');
    }

    public function uploadImage(Request $request) {
        $id = $request->get('id');
        $artist = Artist::find($id);
        $via = $request->get('via');
        if ($via == 'url') {
            $request->validate([
                'url' => 'required|url',
            ]);
            try {
                $url = $request->get('url');
                $artist->downloadImageByUrl($url);
            } catch (\Exception $e) {
                Toast::error($e->getMessage());
                return redirect()->back();
            }
        } else if ($via == 'file') {
            $request->validate([
                'file' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            ]);
            try {
                $file = $request->file('file');
                \Storage::disk('final_artist_image')->put($artist->artist_id . '/avatar.jpg', file_get_contents($file));
                $artist->image_path = $artist->artist_id . '/avatar.jpg';
                $artist->save();
            } catch (\Exception $e) {
                Toast::error($e->getMessage());
                return redirect()->back();
            }
        }
    }

    public function uploadBanner(Request $request)
    {
        $id = $request->get('id');
        $artist = Artist::find($id);
        $via = $request->get('via');
        if ($via == 'url') {
            $request->validate([
                'url' => 'required|url',
            ]);
            try {
                $url = $request->get('url');
                $artist->downloadImageByUrl($url, 'banner');
            } catch (\Exception $e) {
                Toast::error($e->getMessage());
                return redirect()->back();
            }
        } else if ($via == 'file') {
            $request->validate([
                'file' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            ]);
            try {
                $file = $request->file('file');
                \Storage::disk('final_artist_image')->put($artist->artist_id . '/banner.jpg', file_get_contents($file));
                $artist->image_path = $artist->artist_id . '/banner.jpg';
                $artist->save();
            } catch (\Exception $e) {
                Toast::error($e->getMessage());
                return redirect()->back();
            }
        }
    }

    public function delete(Request $request)
    {
        $artist = Artist::find($request->id);
        $songs = $artist
            ->song()
            ->withCount("artist")
            ->having("artist_count", "<=", 1)
            ->get();
        foreach ($songs as $song) {
            $song->display = "private";
            $song->save();
        }
        $artist->song()->detach();
        if($artist->image_path)
            \Storage::disk('final_artist_image')->delete($artist->image_path);
        if($artist->banner_path)
            \Storage::disk('final_artist_image')->delete($artist->banner_path);
        Toast::warning("Artist {$artist->artist_id} deleted");
        $artist->delete();
        return redirect()->route('platform.app.artists');
    }

    public function group(Request $request)
    {
        $from_id = $request->start_id;
        $to_id = $request->endpoint_id;
        $artist_from = Artist::find($from_id);
        $artist_to = Artist::find($to_id);
        if (!$artist_from || !$artist_to) {
           Toast::error("Artist not found");
          return redirect()->back();
        }

        $songs = $artist_from->song()->get();
        foreach ($songs as $song) {
            $song->artist()->detach($artist_from->artist_id);
            if (
                !$song
                    ->artist()
                    ->where("artist_id", $artist_to->artist_id)
                    ->exists()
            ) {
                $song->artist()->attach($artist_to->artist_id);
            }
        }
        Toast::info("Artist {$artist_from->artist_id} grouped to {$artist_to->artist_id}");
        $this->delete(new Request(["id" => $artist_from->artist_id]));
        return redirect()->route('platform.app.artists');
    }
}
