<?php

namespace App\Http\Controllers;

use App\Models\LikedSong;
use Carbon\Carbon;
use App\Models\Song;
use App\Models\User;
use App\Models\Playlist;
use Illuminate\Support\Str;
use App\Models\PlaylistSong;
use Illuminate\Http\Request;
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
    public function deletePlaylistSong($id)
    {
        $playlist = Playlist::where('playlist_id', $id)->first();
        if (Auth::user()->user_id === $playlist->user_id) {
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
        $songList = PlaylistSong::select('song_id', 'created_at')
            ->with('song')
            ->where('playlist_id', $id)
            ->get();
        $songCount = $songList->count();
        $songDuration = array_sum(array_column($songList->toArray(), 'duration'));
        return response()->json([
            'songList' => $songList,
            'songCount' => $songCount,
            'songDuration' => $songDuration
        ]);
    }
    public function authPlaylist()
    {
        $playlist = Playlist::where('user_id', Auth::user()->user_id)->get();
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
                'message' => 'Cannot find playlist'
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
            $playlist->user_id !== Auth::user()->user_id &&
            $playlist->type === 'Private'
        ) {
            return response()->json([
                'status' => 'error',
                'message' => 'You don\'t have permission to add song this playlist'
            ]);
        }
        $playlistSong = new PlaylistSong();
        $playlistSong->playlist_id = $request->playlist_id;
        $playlistSong->song_id = $request->song_id;
        $playlistSong->save();
        return response()->json([
            'status' => 'success',
            'message' => 'Add song to playlist successfully'
        ]);
    }
}
