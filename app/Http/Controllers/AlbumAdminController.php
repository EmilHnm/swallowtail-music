<?php

namespace App\Http\Controllers;

use App\Models\Song;
use App\Models\Album;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AlbumAdminController extends Controller
{
    //
    public function getAll()
    {
        if (Auth::user()->role === "") {
            return response()->json(
                [
                    "message" =>
                        "You are not authorized to access this resource.",
                ],
                403
            );
        } else {
            $albums = Album::with(["user"])
                ->withCount(["song"])
                ->get();
            return response()->json(
                [
                    "albums" => $albums,
                    "status" => "success",
                ],
                200
            );
        }
    }

    public function show($id)
    {
        if (Auth::user()->role === "") {
            return response()->json(
                [
                    "message" =>
                        "You are not authorized to access this resource.",
                ],
                403
            );
        } else {
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
    }

    public function songRemove(Request $request)
    {
        if (Auth::user()->role === "") {
            return response()->json(
                ["message" => "You are not authorized to do this action."],
                403
            );
        } else {
            $song = Song::where("song_id", $request->song_id)->first();
            $song->album_id = null;
            $song->save();
            return response()->json([
                "status" => "success",
                "message" => "Song removed successfully!",
            ]);
        }
    }

    public function songAddable()
    {
        if (Auth::user()->role === "") {
            return response()->json(
                ["message" => "You are not authorized to do this action."],
                403
            );
        } else {
            $songs = Song::where("album_id", "")->get();
        }
    }

    public function songAdd(Request $request)
    {
        if (Auth::user()->role === "") {
            return response()->json(
                ["message" => "You are not authorized to do this action."],
                403
            );
        }
        $album = Album::where("album_id", $request->album_id)->first;
        if (!$album) {
            return response()->json(
                [
                    "status" => "error",
                    "message" => "Album not found!",
                ],
                http_response_code(402)
            );
        }
        $song = Song::where("song_id", $request->song_id)->first();
        if (!$song) {
            return response()->json(
                [
                    "status" => "error",
                    "message" => "Song not found!",
                ],
                http_response_code(402)
            );
        }
        $song->album_id = $album->id;
        $song->save();
        return response()->json(
            [
                "status" => "success",
                "message" => "Add song to album successfully!",
            ],
            http_response_code(200)
        );
    }

    public function delete($id)
    {
        if (Auth::user()->role === "") {
            return response()->json(
                ["message" => "You are not authorized to do this action."],
                403
            );
        }

        $album = Album::find($id);
        if (!$album) {
            return response()->json(
                [
                    "status" => "error",
                    "message" => "Album not found!",
                ],
                http_response_code(402)
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
            http_response_code(200)
        );
    }
}
