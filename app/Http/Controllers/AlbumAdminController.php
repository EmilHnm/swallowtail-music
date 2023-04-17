<?php

namespace App\Http\Controllers;

use App\Models\Song;
use App\Models\Album;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AlbumAdminController extends Controller
{
    //
    public function getAll(Request $request)
    {
        $albums = [];
        $itemPerPage = $request->get("itemPerPage") ?? 10;
        switch ($request->get("type")) {
            case "title":
                $albums = Album::with(["user"])
                    ->where("name", "like", "%" . $request->get("query") . "%")
                    ->withCount(["song"])
                    ->paginate($itemPerPage);
                break;
            case "uploader":
                $albums = Album::with(["user"])
                    ->whereHas("user", function ($query) use ($request) {
                        $query->where(
                            "name",
                            "like",
                            "%" . $request->get("query") . "%"
                        );
                    })
                    ->withCount(["song"])
                    ->paginate($itemPerPage);
                break;
            case "year":
                $albums = Album::with(["user"])
                    ->where(
                        "release_year",
                        "like",
                        "%" . $request->get("query") . "%"
                    )
                    ->withCount(["song"])
                    ->paginate($itemPerPage);
                break;
            default:
                $albums = Album::with(["user"])
                    ->withCount(["song"])
                    ->paginate($itemPerPage);
                break;
        }
        return response()->json(
            [
                "albums" => $albums,
                "status" => "success",
            ],
            200
        );
    }

    public function show($id)
    {
        $album = Album::with(["user"])
            ->withCount(["song"])
            ->find($id);
        $songs = Song::where("album_id", $id)->get();
        return response()->json([
            "status" => "success",
            "album" => $album,
            "songs" => $songs,
        ]);
    }

    public function song($id)
    {
        $songs = Song::where("album_id", $id)->get();
        return response()->json([
            "status" => "success",
            "songs" => $songs,
        ]);
    }

    public function songRemove(Request $request)
    {
        $song = Song::where("song_id", $request->song_id)->first();
        $song->album_id = null;
        $song->save();
        return response()->json([
            "status" => "success",
            "message" => "Song removed successfully!",
        ]);
    }

    public function getSongNotInAlbum(Request $request)
    {
        $query = urldecode($request->get("query"));
        $songs = Song::with("artist")
            ->where("album_id", null)
            ->where("title", "like", "%" . $query . "%")
            ->get();
        return response()->json([
            "status" => "success",
            "songs" => $songs,
        ]);
    }

    public function songAdd(Request $request)
    {
        $album = Album::find($request->album_id);
        $album = Album::where("album_id", $request->album_id)->first();
        if (!$album) {
            return response()->json(
                [
                    "status" => "error",
                    "message" => "Album not found!",
                ],
                402
            );
        }
        $song = Song::where("song_id", $request->song_id)->first();
        if (!$song) {
            return response()->json(
                [
                    "status" => "error",
                    "message" => "Song not found!",
                ],
                402
            );
        }
        $song->album_id = $album->album_id;
        $song->save();
        return response()->json(
            [
                "status" => "success",
                "message" => "Add song to album successfully!",
            ],
            200
        );
    }

    public function update(Request $request)
    {
        $albumData = json_decode($request->albumData);
        $album = Album::where("album_id", $albumData->album_id)->first();
        if (!$album) {
            return response()->json([
                "status" => "error",
                "message" => "Album not found!",
            ]);
        }
        if ($album->user_id != Auth::user()->user_id) {
            return response()->json([
                "status" => "error",
                "message" => "You are not allowed to update this album!",
            ]);
        }
        if ($request->file("image")) {
            $imageFile = $request->file("image");
            $fileName =
                $album->album_id .
                "." .
                $imageFile->getClientOriginalExtension();
            @unlink(
                public_path("storage/upload/album_cover") .
                    "/" .
                    $album->image_path
            );
            $imageFile->move(
                public_path("storage/upload/album_cover"),
                $fileName
            );
            $album->image_path = $fileName;
        }
        $album->name = $albumData->name;
        $album->release_year = $albumData->release_year;
        $album->type = $albumData->type;
        $album->save();
        return response()->json([
            "status" => "success",
            "message" => "Album updated successfully!",
        ]);
    }

    public function delete($id)
    {
        $album = Album::find($id);
        if (!$album) {
            return response()->json(
                [
                    "status" => "error",
                    "message" => "Album not found!",
                ],
                402
            );
        }
        $songs = Song::where("album_id", $id)->get();
        foreach ($songs as $song) {
            $song->album_id = null;
            $song->save();
        }
        @unlink(
            public_path("storage/upload/album_cover/") . "/" . $album->album_id
        );
        $album->delete();
        return response()->json(
            [
                "status" => "success",
                "message" => "Delete album successfully!",
            ],
            200
        );
    }
}
