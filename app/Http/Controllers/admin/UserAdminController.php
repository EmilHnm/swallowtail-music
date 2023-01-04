<?php

namespace App\Http\Controllers\admin;

use App\Models\Song;
use App\Models\User;
use App\Models\Album;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class UserAdminController extends Controller
{
    //
    public function getAll()
    {
        $users = User::get();
        return response()->json(
            [
                "users" => $users,
                "status" => "success",
            ],
            200
        );
    }

    public function show($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(
                [
                    "message" => "User not found",
                    "status" => "error",
                ],
                404
            );
        } else {
            return response()->json(
                [
                    "user" => $user,
                    "status" => "success",
                ],
                200
            );
        }
    }

    public function showUserUploadSong($id)
    {
        $songs = DB::select(
            'SELECT songs.*,
                    artists.name AS artist_name,artists.artist_id AS artist_id,
                    albums.name AS album_name,albums.album_id AS album_id
                        FROM songs
                        LEFT JOIN song_artists ON songs.song_id = song_artists.song_id
                        LEFT JOIN artists ON song_artists.artist_id =artists.artist_id
                        LEFT JOIN albums ON songs.album_id = albums.album_id
                        WHERE songs.user_id = ?',
            [$id]
        );

        return response()->json([
            "status" => "success",
            "songs" => $songs,
        ]);
    }

    public function updateRole($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(
                [
                    "message" => "User not found",
                    "status" => "error",
                ],
                404
            );
        } else {
            if ($user->role === "Mod") {
                $user->role = "";
            } else {
                $user->role = "Mod";
            }
            $user->save();
            return response()->json(
                [
                    "message" => "Update role successfully",
                    "status" => "success",
                ],
                200
            );
        }
    }

    public function deleteUser($id)
    {
        if (Auth::user()->user_id === $id) {
            return response()->json(
                [
                    "status" => "error",
                    "message" =>
                        "You are not authorized to access this resource.",
                ],
                403
            );
        } else {
            $user = User::find($id);
            if (!$user) {
                return response()->json(
                    [
                        "message" => "User not found",
                        "status" => "error",
                    ],
                    404
                );
            } else {
                if ($user->role === "Admin") {
                    return response()->json(
                        [
                            "message" => "You can not delete admin",
                            "status" => "error",
                        ],
                        403
                    );
                }
                DB::table("personal_access_tokens")
                    ->where("tokenable_id", $id)
                    ->delete();
                $user->delete();
                return response()->json(
                    [
                        "message" => "User deleted successfully",
                        "status" => "success",
                    ],
                    200
                );
            }
        }
    }

    public function showUserUploadAlbum($id)
    {
        $albumUploaded = DB::table("albums")
            ->leftJoin("songs", "albums.album_id", "=", "songs.album_id")
            ->select("albums.*", DB::raw("count(songs.song_id) as songCount"))
            ->where("albums.user_id", Auth::user()->user_id)
            ->groupBy("albums.id")
            ->where("albums.user_id", $id)
            ->get();
        return response()->json([
            "status" => "success",
            "album" => $albumUploaded,
        ]);
    }

    public function deleteAlbum($id)
    {
        $album = Album::find($id);
        if (!$album->id) {
            return response()->json([
                "status" => "error",
                "message" => "Album not found",
            ]);
        }
        @unlink(
            public_path("storage/upload/album_cover/") . "/" . $album->album_id
        );
        $song = Song::where("album_id", $album->album_id)->get();
        foreach ($song as $s) {
            $s->album_id = null;
            $s->save();
        }
        $album->delete();
        return response()->json([
            "status" => "success",
            "message" => "Album deleted successfully!",
        ]);
    }
}
