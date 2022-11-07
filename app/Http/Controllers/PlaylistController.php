<?php

namespace App\Http\Controllers;

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
        $playlist->playlist_id = Str::random(5) . Carbon::now()->timestamp . '-' . Str::random(5);
        $playlist->title = Auth::user()->name . "'s Playlist";
        $playlist->description = "";
        $playlist->type = "Private";
        $playlist->save();
        return response()->json([
            'status' => 'success',
            'playlist_id' => $playlist->playlist_id
        ]);
    }
    public function deletePlaylist($id)
    {
        $playlist = Playlist::where('playlist_id', $id)->first();
        if (Auth::user()->user_id === $playlist->user_id) {
            @unlink(public_path('storage/upload/playlist_cover') . "/" . $playlist->image_path);
            PlaylistSong::where('playlist_id', $id)->delete();
            $playlist->delete();
            return response()->json([
                'status' => 'success',
                'message' => 'Playlist deleted successfully'
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'You are not allowed to delete this playlist'
            ]);
        }
    }
    public function getPlaylist($id)
    {
        $playlist = Playlist::where('playlist_id', $id)->first();
        if (!$playlist) {
            return response()->json([
                'status' => 'error',
                'message' => 'Cannot find playlist'
            ]);
        }
        if (
            $playlist->user_id !== Auth::user()->user_id &&
            $playlist->type === 'Private'
        ) {
            return response()->json([
                'status' => 'error',
                'message' => 'You don\'t have permission to play this playlist'
            ]);
        }
        $owner = User::where('user_id', $playlist->user_id)->first();
        return response()->json(['playlistDetail' => $playlist, 'owner' => $owner]);
    }
    public function getPlaylistSong($id)
    {
        $playlist = Playlist::where('playlist_id', $id)->first();
        if (!$playlist) {
            return response()->json([
                'status' => 'error',
                'message' => 'Cannot find playlist'
            ]);
        }
        if (
            $playlist->user_id !== Auth::user()->user_id &&
            $playlist->type === 'Private'
        ) {
            return response()->json([
                'status' => 'error',
                'message' => 'You don\'t have permission to play this playlist'
            ]);
        }
        // $songList = PlaylistSong::select('song_id', 'created_at')
        //     ->with('song')
        //     ->where('playlist_id', $id)
        //     ->get();

        // $songDuration = array_sum(array_column($songList->toArray(), 'duration'));
        $songList = DB::table('playlist_songs')
            ->join('songs', 'playlist_songs.song_id', '=', 'songs.song_id')
            ->join('song_artists', 'songs.song_id', '=', 'song_artists.song_id')
            ->join('artists', 'song_artists.artist_id', '=', 'artists.artist_id')
            ->join('albums', 'songs.album_id', '=', 'albums.album_id')
            ->select(
                'songs.song_id',
                'songs.title',
                'songs.song_id',
                'songs.duration',
                'playlist_songs.created_at',
                "artists.artist_id",
                "artists.name as artist_name",
                "albums.name as album_name",
                "albums.album_id as album_id",
                "albums.image_path as image_path",
                "playlist_songs.created_at as added_date"
            )
            ->where('playlist_songs.playlist_id', $id)
            ->orderBy('playlist_songs.created_at', 'desc')
            ->get();
        $songCount = $songList->count();
        return response()->json([
            'songList' => $songList,
            'songCount' => $songCount,
            // 'songDuration' => $songDuration
        ]);
    }
    public function authPlaylist()
    {
        //$playlist = Playlist::where('user_id', Auth::user()->user_id)->get();
        $playlist = DB::table("playlists")
            ->leftJoin('playlist_songs', 'playlists.playlist_id', "=", "playlist_songs.playlist_id")
            ->select('playlists.*', DB::raw('count(playlist_songs.song_id) as songCount'))
            ->where('playlists.user_id', Auth::user()->user_id)
            ->groupBy('playlists.playlist_id')
            ->get();
        return response()->json(['playlist' => $playlist]);
    }
    public function authLikedList()
    {
        $likedList = LikedSong::select('song_id', 'created_at')
            ->with('song')
            ->where('user_id', Auth::user()->user_id)
            ->get();
        $totalDuration = array_sum(array_column($likedList->toArray(), 'duration'));
        return response()->json([
            'likedList' => $likedList,
            'songCount' => $likedList->count(),
            'totalDuration' => $totalDuration
        ]);
    }
    public function addSongToPlaylist(Request $request)
    {
        $playlist = Playlist::where('playlist_id', $request->playlist_id)->first();
        $song = Song::where('song_id', $request->song_id)->first();
        if (!$song) {
            return response()->json([
                'status' => 'error',
                'message' => 'Cannot find song'
            ]);
        }
        if ($song->user_id != Auth::user()->user_id && $song->display === 'private') {
            return response()->json([
                'status' => 'error',
                'message' => 'You are not allowed to add this song to playlist'
            ]);
        }
        if (!$playlist) {
            return response()->json([
                'status' => 'error',
                'message' => 'Cannot find playlist'
            ]);
        }
        if (
            $playlist->user_id !== Auth::user()->user_id
        ) {
            return response()->json([
                'status' => 'error',
                'message' => 'You don\'t have permission to add song this playlist'
            ]);
        }
        $playlist->song()->attach($request->song_id);
        return response()->json([
            'status' => 'success',
            'message' => 'Add song to playlist successfully'
        ]);
    }

    public function addAlbumToPlaylist(Request $request)
    {
        $playlist = Playlist::where('playlist_id', $request->playlist_id)->first();
        $songs = DB::table('songs')
            ->select('song_id')
            ->whereNotIn('song_id', function ($query) use ($request) {
                $query->select('song_id')
                    ->from('playlist_songs')
                    ->where('playlist_id', $request->playlist_id);
            })
            ->where('album_id', $request->album_id)
            ->get()->toArray();
        if (!$playlist) {
            return response()->json([
                'status' => 'error',
                'message' => 'Cannot find playlist'
            ]);
        }
        if (
            $playlist->user_id !== Auth::user()->user_id
        ) {
            return response()->json([
                'status' => 'error',
                'message' => 'You don\'t have permission to add song this playlist'
            ]);
        }
        $playlist->song()->attach(
            array_column($songs, "song_id")
        );
        return response()->json([
            'status' => 'success',
            'message' => 'Added Successfully'
        ]);
    }

    public function getAddableListSong(Request $request, $id)
    {
        if (!$request->has('query')) {
            return;
        }
        $query = "%"  . $request->input('query') . "%";
        $playlist_id = $id;
        $songs = DB::table('songs')
            ->whereNotIn(
                'songs.song_id',
                fn ($q) =>
                $q->select('song_id')
                    ->from('playlist_songs')
                    ->where('playlist_id', $playlist_id)
                    ->get()
            )
            ->join('song_artists', 'songs.song_id', '=', 'song_artists.song_id')
            ->join('artists', 'song_artists.artist_id', '=', 'artists.artist_id')
            ->join('albums', 'songs.album_id', '=', 'albums.album_id')
            ->select(
                'songs.song_id',
                'songs.title',
                'songs.song_id',
                'songs.duration',
                'songs.listens',
                "artists.artist_id",
                "artists.name as artist_name",
                "albums.name as album_name",
                "albums.album_id as album_id",
                "albums.image_path as image_path",
            )
            ->where('songs.title', 'like',  $query)
            ->limit(5)
            ->get();
        return response()->json([
            'status' => 'success',
            'songs' => $songs
        ]);
    }

    public function addToPlaylist(Request $request)
    {
        $playlist_source = Playlist::where('playlist_id', $request->from)->first();
        $playlist_destination = Playlist::where('playlist_id', $request->to)->first();
        if (
            $playlist_destination->user_id !== Auth::user()->user_id ||
            ($playlist_source->user_id !== Auth::user()->user_id
                && $playlist_source->display === 'Private')
        ) {
            return response()->json([
                'status' => 'error',
                'message' => 'You don\'t have permission to add song this playlist'
            ]);
        }
        $song_list = DB::table('playlist_songs')
            ->where('playlist_id', $request->from)
            ->whereNotIn(
                'song_id',
                fn ($q) =>
                $q->select('song_id')
                    ->from('playlist_songs')
                    ->where('playlist_id', $request->to)
                    ->get()
            )
            ->select('playlist_songs.song_id')
            ->get()->toArray();
        $playlist_destination->song()->attach(array_column($song_list, 'song_id'));
        return response()->json([
            'status' => 'success',
            'message' => 'Add song to playlist successfully'
        ]);
    }
    public function updatePlaylist(Request $request)
    {
        $playlist = Playlist::where('playlist_id', $request->playlist_id)->first();
        if (!$playlist) {
            return response()->json([
                'status' => 'error',
                'message' => 'Cannot find playlist'
            ]);
        }
        if (
            $playlist->user_id !== Auth::user()->user_id
        ) {
            return response()->json([
                'status' => 'error',
                'message' => 'You don\'t have permission to update this playlist'
            ]);
        }
        if ($request->file('image')) {
            $image = $request->file('image');
            $imageName = $request->playlist_id . '.' . $image->getClientOriginalExtension();
            @unlink(public_path('storage/upload/playlist_cover') . $imageName);
            $image->move(public_path('storage/upload/playlist_cover'), $imageName);
            $playlist->image_path = $imageName;
        }
        $playlist->title = $request->title;
        $playlist->description = $request->description;
        $playlist->save();
        return response()->json([
            'status' => 'success',
            'message' => 'Update playlist successfully'
        ]);
    }

    public function settypePlaylist(Request $request)
    {
        $playlist_id = $request->playlist_id;
        $type = $request->type;
        $playlist = Playlist::where('playlist_id', $playlist_id)->first();
        if ($playlist->user_id != Auth::user()->user_id) {
            return response()->json([
                'status' => 'error',
                'message' => 'You don\'t have permission to update this playlist'
            ]);
        }
        $playlist->type = $type;
        $playlist->save();
        return response()->json([
            'status' => 'success',
            'message' => 'Update playlist type successfully'
        ]);
    }
}
