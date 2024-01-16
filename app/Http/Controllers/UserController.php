<?php

namespace App\Http\Controllers;

use App\Models\Album;
use App\Models\Song;
use App\Models\User;
use App\Models\Playlist;
use Termwind\Components\Dd;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    //
    public function show($id)
    {
        $user = User::where("user_id", $id)
            ->select("users.name", "users.user_id", "users.profile_photo_url")
            ->withTrashed()
            ->first();
        if (!$user) {
            return response()->json([
                "status" => "error",
                "message" => "User Not Found",
            ]);
        }
        $publicPlaylist_count = Playlist::where("user_id", $id)
            ->where("type", "Public")
            ->count();
        $songUploaded_count = Song::where("user_id", $id)
            ->where("display", "public")
            ->count();
        $albumUploaded_count = Album::where("user_id", $id)->count();
        $user->publicPlaylist_count = $publicPlaylist_count;
        $user->songUploaded_count = $songUploaded_count;
        $user->albumUploaded_count = $albumUploaded_count;
        return response()->json([
            "status" => "success",
            "user" => $user,
        ]);
    }
    public function searchUser(Request $request)
    {
        if ($request->query->has("query")) {
            $query = $request->query->get("query");
            $users = DB::table("users")
                ->join("songs", "users.user_id", "=", "songs.user_id")
                ->select(
                    "users.name",
                    "users.user_id",
                    "users.profile_photo_url",
                    DB::raw("count(songs.song_id) as song_count")
                )
                ->where("name", "like", "%" . $query . "%")
                ->groupBy("users.user_id")
                ->get();
            return response()->json([
                "status" => "success",
                "users" => $users,
            ]);
        } else {
            return response()->json([
                "status" => "error",
                "message" => "Query not found",
            ]);
        }
    }
    public function getUserTopArtist($id)
    {
        $artist = DB::table("artists")
            ->leftJoin(
                "song_artists",
                "artists.artist_id",
                "=",
                "song_artists.artist_id"
            )
            ->leftJoin("songs", "song_artists.song_id", "=", "songs.song_id")
            ->select("artists.*", DB::raw("sum(songs.listens) as total"))
            ->groupBy("artists.artist_id")
            ->where("songs.user_id", $id)
            ->limit(8)
            ->get();
        return response()->json([
            "status" => "success",
            "artists" => $artist,
        ]);
    }

    public function getTopTracks($id)
    {
        $songs = Song::with(["artist", "album", "like"])
            ->where("user_id", $id)
            ->orderBy("listens", "desc")
            ->limit(5)
            ->get();
        return response()->json([
            "status" => "success",
            "songs" => $songs,
        ]);
    }

    public function getPublicPlaylist($id)
    {
        $playlists = DB::table("playlists")
            ->leftJoin(
                "playlist_songs",
                "playlists.playlist_id",
                "=",
                "playlist_songs.playlist_id"
            )
            ->select(
                "playlists.*",
                DB::raw("count(playlist_songs.song_id) as songCount")
            )
            ->where("playlists.user_id", $id)
            ->where("playlists.type", "Public")
            ->groupBy("playlists.playlist_id")
            ->get();
        return response()->json([
            "status" => "success",
            "playlists" => $playlists,
        ]);
    }
}
