<?php

namespace App\Http\Controllers;

use App\Enum\SongMetadataStatusEnum;
use App\Models\Album;
use Carbon\Carbon;
use App\Models\Song;
use App\Models\User;
use App\Models\Playlist;
use App\Models\LikedSong;
use Illuminate\Support\Str;
use App\Models\PlaylistSong;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PlaylistController extends Controller
{
    //
    public function createPlaylist()
    {
        $playlist = new Playlist();
        $playlist->user_id = Auth::user()->user_id;
        $playlist->playlist_id =
            Str::random(5) . Carbon::now()->timestamp . "-" . Str::random(5);
        $playlist->title = Auth::user()->name . "'s Playlist";
        $playlist->description = "";
        $playlist->type = "Private";
        $playlist->save();
        return response()->json([
            "status" => "success",
            "playlist" => $playlist,
        ]);
    }
    public function deletePlaylist($id)
    {
        $playlist = Playlist::where("playlist_id", $id)->first();
        if (!$playlist) {
            return response()->json([
                "status" => "error",
                "message" => "Cannot find this playlist!",
            ]);
        }
        if (Auth::user()->user_id == $playlist->user_id) {
            @unlink(
                public_path("storage/upload/playlist_cover") .
                    "/" .
                    $playlist->image_path
            );
            PlaylistSong::where("playlist_id", $id)->delete();
            $playlist->delete();
            return response()->json([
                "status" => "success",
                "message" => "Playlist deleted successfully",
            ]);
        } else {
            return response()->json([
                "status" => "error",
                "message" => "You are not allowed to delete this playlist",
            ]);
        }
    }
    public function getPlaylist($id)
    {
        $playlist = Playlist::where("playlist_id", $id)->first();
        if (!$playlist) {
            return response()->json([
                "status" => "error",
                "message" => "Cannot find playlist",
            ]);
        }
        if (
            $playlist->user_id != Auth::user()->user_id &&
            $playlist->type === "Private"
        ) {
            return response()->json([
                "status" => "error",
                "message" => 'You don\'t have permission to play this playlist',
            ]);
        }
        $owner = User::where("user_id", $playlist->user_id)->first();
        return response()->json([
            "playlistDetail" => $playlist,
            "owner" => $owner,
        ]);
    }
    public function getPlaylistSong($id)
    {
        $playlist = Playlist::where("playlist_id", $id)->first();
        if (!$playlist) {
            return response()->json([
                "status" => "error",
                "message" => "Cannot find playlist",
            ]);
        }
        if (
            $playlist->user_id != Auth::user()->user_id &&
            $playlist->type === "Private"
        ) {
            return response()->json([
                "status" => "error",
                "message" => 'You don\'t have permission to play this playlist',
            ]);
        }

        $songList = Song::where("display", "public")
            ->with([
                "artist",
                "album",
                "like" => function ($query) {
                    $query->where("user_id", Auth::user()->user_id);
                },
            ])
            ->whereHas("playlist", function ($query) use ($id) {
                $query->where("playlists.playlist_id", $id);
            })
            ->get();
        return response()->json([
            "songList" => $songList,
        ]);
    }
    public function authPlaylist()
    {
        $playlist = Playlist::withCount('song')->where('user_id', Auth::user()->user_id)->get();
        return response()->json(["playlist" => $playlist]);
    }
    public function authLikedList()
    {
        $likedList = Song::where("display", "public")
            ->with([
                "artist",
                "album",
                "like"
            ])
            ->whereHas("like", function ($query) {
                $query->where("user_id", Auth::user()->user_id);
            })
            ->get();
        return response()->json([
            "status" => "success",
            "likedList" => $likedList,
        ]);
    }
    public function addSongToPlaylist(Request $request)
    {
        $playlist = Playlist::where(
            "playlist_id",
            $request->playlist_id
        )->first();
        $song = Song::where("song_id", $request->song_id)->first();
        if (!$song) {
            return response()->json([
                "status" => "error",
                "message" => "Cannot find song",
            ]);
        }
        if (
            $song->user_id != Auth::user()->user_id &&
            $song->display == "private"
        ) {
            return response()->json([
                "status" => "error",
                "message" => "You are not allowed to add this song to playlist",
            ]);
        }
        if (!$playlist) {
            return response()->json([
                "status" => "error",
                "message" => "Cannot find playlist",
            ]);
        }
        if ($playlist->user_id != Auth::user()->user_id) {
            return response()->json([
                "status" => "error",
                "message" =>
                'You don\'t have permission to add song this playlist',
            ]);
        }
        $playlist->song()->attach($request->song_id);
        return response()->json([
            "status" => "success",
            "message" => "Add song to playlist successfully",
        ]);
    }

    public function addAlbumToPlaylist(Request $request)
    {
        $playlist = Playlist::where(
            "playlist_id",
            $request->playlist_id
        )->first();
        $songs = DB::table("songs")
            ->select("song_id")
            ->whereNotIn("song_id", function ($query) use ($request) {
                $query
                    ->select("song_id")
                    ->from("playlist_songs")
                    ->where("playlist_id", $request->playlist_id);
            })
            ->where("album_id", $request->album_id)
            ->get()
            ->toArray();
        if (!$playlist) {
            return response()->json([
                "status" => "error",
                "message" => "Cannot find playlist",
            ]);
        }
        if ($playlist->user_id !== Auth::user()->user_id) {
            return response()->json([
                "status" => "error",
                "message" =>
                'You don\'t have permission to add song this playlist',
            ]);
        }
        $playlist->song()->attach(array_column($songs, "song_id"));
        return response()->json([
            "status" => "success",
            "message" => "Added Successfully",
        ]);
    }

    public function getAddableListSong(Request $request, $id)
    {
        if (!$request->has("query")) {
            return response()->json([
                "status" => "error",
                "message" => "Query is required",
            ]);
        }
        $query = $request->input("query") ;
        $playlist_id = $id;
        try {
            $songs = Song::search("title:($query~1)^3 or normalized_title:($query~1)^2 or ($query) or album.name:($query~1) or artist.name:($query~1)")
                ->whereNotIn(
                    "songs.song_id",
                    fn ($q) => $q
                        ->select("song_id")
                        ->from("playlist_songs")
                        ->where("playlist_id", $playlist_id)
                        ->get()->toArray()
                )
                ->take(5)->get()
                ->load(['artist', 'album', 'like']);
        } catch(\Throwable $th) {
            $songs = Song::whereNotIn(
                "songs.song_id",
                fn ($q) => $q
                    ->select("song_id")
                    ->from("playlist_songs")
                    ->where("playlist_id", $playlist_id)
                    ->get()
            )
                ->where("songs.title", "like", "%" .  $query . "%")
                ->with(["artist", "album", "like"])
                ->limit(5)
                ->get();
        }

        return response()->json([
            "status" => "success",
            "songs" => $songs,
        ]);
    }

    public function addToPlaylist(Request $request)
    {
        $playlist_source = Playlist::where(
            "playlist_id",
            $request->from
        )->first();
        $playlist_destination = Playlist::where(
            "playlist_id",
            $request->to
        )->first();
        if (
            $playlist_destination->user_id != Auth::user()->user_id ||
            ($playlist_source->user_id != Auth::user()->user_id &&
                $playlist_source->display === "Private")
        ) {
            return response()->json([
                "status" => "error",
                "message" =>
                'You don\'t have permission to add song this playlist',
            ]);
        }
        $song_list = DB::table("playlist_songs")
            ->where("playlist_id", $request->from)
            ->whereNotIn(
                "song_id",
                fn ($q) => $q
                    ->select("song_id")
                    ->from("playlist_songs")
                    ->where("playlist_id", $request->to)
                    ->get()
            )
            ->select("playlist_songs.song_id")
            ->get()
            ->toArray();
        $playlist_destination
            ->song()
            ->attach(array_column($song_list, "song_id"));
        return response()->json([
            "status" => "success",
            "message" => "Add song to playlist successfully",
        ]);
    }
    public function updatePlaylist(Request $request)
    {
        $playlist = Playlist::where(
            "playlist_id",
            $request->playlist_id
        )->first();
        if (!$playlist) {
            return response()->json([
                "status" => "error",
                "message" => "Cannot find playlist",
            ]);
        }
        if ($playlist->user_id != Auth::user()->user_id) {
            return response()->json([
                "status" => "error",
                "message" =>
                'You don\'t have permission to update this playlist',
            ]);
        }
        if ($request->file("image")) {
            $image = $request->file("image");
            $imageName =
                $request->playlist_id .
                "." .
                $image->getClientOriginalExtension();
            @unlink(public_path("storage/upload/playlist_cover") . $imageName);
            $image->move(
                public_path("storage/upload/playlist_cover"),
                $imageName
            );
            $playlist->image_path = $imageName;
        }
        $playlist->title = $request->title;
        $playlist->description = $request->description;
        $playlist->save();
        return response()->json([
            "status" => "success",
            "message" => "Update playlist successfully",
        ]);
    }

    public function settypePlaylist(Request $request)
    {
        $playlist_id = $request->playlist_id;
        $type = $request->type;
        $playlist = Playlist::where("playlist_id", $playlist_id)->first();
        if ($playlist->user_id != Auth::user()->user_id) {
            return response()->json([
                "status" => "error",
                "message" =>
                'You don\'t have permission to update this playlist',
            ]);
        }
        $playlist->type = $type;
        $playlist->save();
        return response()->json([
            "status" => "success",
            "message" => "Update playlist type successfully",
        ]);
    }
}
