<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Song;
use App\Models\Album;
use App\Enum\AlbumTypeEnum;
use Illuminate\Support\Str;
use App\Orchid\Helpers\Text;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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


        $album->name = $name;
        $album->normalized_name = Text::normalize($name);
        $album->album_id = $album_id;
        $album->user_id = Auth::user()->user_id;
        $album->release_year = $release_year;
        $album->type = $type;
        $songData = [];
        for ($i = 0; $i < $songCount; $i++) {
            $song = new Song();
            $song->song_id = "song_" . date("YmdHi") . Str::random(10);
            $song->user_id = Auth::user()->user_id;
            $song->title = $request["songName_" . $i];
            $song->normalized_title = Text::normalize($request["songName_" . $i]);
            $song->display = "private";
            $song->album_id = $album_id;
            $songData[] = (object)[
                "song_id" => $song->song_id,
                "index" => $i,
            ];
            $song->save();
        }
        $album->save();

        if ($request->file("albumImage")) {
            $imageFile = $request->file("albumImage");
            $file_path = file_path_helper($album->id) .
                $album_id . "." . $imageFile->getClientOriginalExtension();
            \Storage::disk('final_cover')->delete($file_path);
            \Storage::disk('final_cover')->put($file_path, file_get_contents($imageFile));
            $album->image_path = $file_path;
        }

        return response()->json([
            "status" => "success",
            "message" => 'Album uploaded successfully!
            All songs uploaded will be ready in some minutes and saved as private by default when finish.
            Please go to Account/Upload Management/Song to update song information.',
            "songs" => $songData,
        ]);
    }

    public function getUploadedAlbum(Request $request)
    {
        $albumUploaded = Album::withCount("song")
            ->where("albums.user_id", Auth::user()->user_id)
            ->when($request->has("query"), function ($query) use ($request) {
                $query->where("name", "like", "%" . $request->get("query") . "%");
            })
            ->when($request->has("sort") && $request->get("order"), function ($query) use ($request) {
                $query->orderBy($request->get("sort"), $request->get("order"));
            })->paginate();
        return response()->json([
            "status" => "success",
            "albums" => $albumUploaded,
        ]);
    }

    public function getAlbumInfo($id)
    {
        $album = Album::withCount('song')->where("album_id", $id)->first();
        return response()->json([
            "status" => "success",
            "album" => $album,
        ]);
    }

    public function getAlbumSongs($id)
    {
        $songList = Song::where("album_id", $id)
            ->where("display", "public")
            ->with([
                "artist",
                "album",
                "like" => function ($query) {
                    $query->where("user_id", Auth::user()->user_id);
                },
            ])
            ->when(\Auth::user()->user_id == Album::find($id)->user_id, function ($query) {
                $query->withoutGlobalScopes(['playable']);
            })
            ->get();
        return response()->json([
            "status" => "success",
            "songs" => $songList,
        ]);
    }

    public function removeAlbumSong(Request $request)
    {
        $song = Song::where("song_id", $request->song_id)->first();
        if ($song->user_id != Auth::user()->user_id) {
            return response()->json([
                "status" => "error",
                "message" => "You are not allowed to delete this song!",
            ]);
        }
        $song->album_id = null;
        $song->save();
        return response()->json([
            "status" => "success",
            "message" => "Song removed successfully!",
        ]);
    }

    public function addAlbumSong(Request $request)
    {
        $song = Song::where("song_id", $request->song_id)->first();
        if ($song->user_id != Auth::user()->user_id) {
            return response()->json([
                "status" => "error",
                "message" => "You are not allowed to delete this song!",
            ]);
        }
        $album = Album::where("album_id", $request->album_id)->first();
        if (!$album) {
            return response()->json([
                "status" => "error",
                "message" => "Album not found!",
            ]);
        }
        $song->album_id = $request->album_id;
        $song->save();
        return response()->json([
            "status" => "success",
            "message" => "Song removed successfully!",
        ]);
    }

    public function updateAlbum(Request $request)
    {
        $albumData = json_decode($request->albumData);
        $album = Album::where("album_id", $albumData->album_id)->first();
        if (!$album) {
            return response()->json([
                "status" => "error",
                "message" => "Album not found!",
            ], 404);
        }
        if ($album->user_id != Auth::user()->user_id) {
            return response()->json([
                "status" => "error",
                "message" => "You are not allowed to update this album!",
            ], 403);
        }
        if ($request->file("image")) {
            $imageFile = $request->file("image");
            $fileName = file_path_helper($album->id) .
                $album->id . "." . $imageFile->getClientOriginalExtension();
            \Storage::disk('final_cover')->delete($album->image_path);
            \Storage::disk('final_cover')->put($fileName, file_get_contents($imageFile));
            $album->image_path = $fileName;
        }
        $album->name = $albumData->name;
        $album->release_year = $albumData->release_year;
        $album->type = $albumData->type;
        $album->save();
        $album->searchable();
        return response()->json(
            [
                "status" => "success",
                "message" => "Album updated successfully!",
            ]
        );
    }

    public function deleteAlbum($id)
    {
        $album = Album::find($id);
        if (!$album->id) {
            return response()->json([
                "status" => "error",
                "message" => "Album not found",
            ], 404);
        }
        if ($album->user_id != Auth::user()->user_id) {
            return response()->json([
                "status" => "error",
                "message" => "You are not allowed to delete this album!",
            ], 403);
        }
        if ($album->image_path) {
            \Storage::disk('final_cover')->delete($album->image_path);
        }
        $songs = Song::where("album_id", $album->album_id)->get();
        foreach ($songs as $song) {
            $song->album_id = null;
            $song->save();
        }
        $album->delete();
        return response()->json([
            "status" => "success",
            "message" => "Album deleted successfully!",
        ]);
    }

    public function getLatestAlbum()
    {
        $albums = Album::with(['user'])
            ->withCount(['song' => fn ($query) => $query->where('display', 'public')])
            ->having('song_count', '>', 0)
            ->take(8)
            ->orderBy('created_at', 'desc')
            ->get();
        return response()->json([
            "status" => "success",
            "albums" => $albums,
        ]);
    }

    public function searchAlbum(Request $request)
    {
        if ($request->query->has("query")) {
            $query = Text::normalize($request->query->get("query"));
            try {
                $albums = Album::search("name:($query~2)^2 or normalized_name:($query~2)^1.5 or ($query)")
                    ->get()->loadCount("song")->filter(
                        function ($album) {
                            return $album->song_count > 0;
                        }
                    )->take(8);
            } catch (\Exception $e) {
                $albums = Album::withCount("song")
                    ->having('song_count', '>', 0)
                    ->where("name", "like", "%" . $query . "%")
                    ->take(8)
                    ->get();
            }

            return response()->json([
                "status" => "success",
                "albums" => $albums,
            ]);
        } else {
            return response()->json([
                "status" => "error",
                "message" => "Query not found",
            ]);
        }
    }

    public function getTopAlbum()
    {

        $album = Album::with(['user'])
            ->withCount(['song' => fn ($query) => $query->where('display', 'public')])
            ->having('song_count', '>', 0)
            ->withSum('song', 'listens')
            ->orderBy('song_sum_listens', 'desc')
            ->take(8)
            ->get();
        return response()->json([
            "status" => "success",
            "albums" => $album,
        ]);
    }

    public function getSongNotInAlbum(Request $request)
    {
        $query = urldecode($request->get("query"));
        $songs = Song::with("artist")
            ->where("user_id", Auth::user()->user_id)
            ->where("title", "LIKE", "%" . $query . "%")
            ->where("album_id", null)
            ->orWhere("album_id", "")
            ->get();
        return response()->json([
            "status" => "success",
            "songs" => $songs,
        ]);
    }

    public function getAlbumType()
    {
        return response()->json([
            "status" => "success",
            "types" => AlbumTypeEnum::toArray(),
        ]);
    }
}
