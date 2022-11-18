<?php

namespace App\Http\Controllers\admin;

use App\Models\Song;
use App\Models\User;
use App\Models\Album;
use App\Models\Playlist;
use App\Models\LikedSong;
use App\Models\SongGenre;
use App\Models\SongArtist;
use App\Models\PlaylistSong;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class UserAdminController extends Controller
{
    //
    public function getAll()
    {
        if (Auth::user()->role !== 'Admin') {
            return response()->json(['message' => 'You are not authorized to access this resource.'], 403);
        } else {
            $users = User::get();
            return response()->json([
                'users' => $users,
                'status' => "success"
            ], 200);
        }
    }

    public function show($id)
    {
        if (Auth::user()->role !== 'Admin') {
            return response()->json(['message' => 'You are not authorized to access this resource.'], 403);
        } else {
            $user = User::find($id);
            if (!$user) {
                return response()->json([
                    'message' => 'User not found',
                    'status' => 'error'
                ], 404);
            } else {
                return response()->json([
                    'user' => $user,
                    'status' => 'success'
                ], 200);
            }
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
            'status' => 'success',
            'songs' => $songs
        ]);
    }

    public function deleteUser($id)
    {
        if (Auth::user()->role !== 'Admin' && Auth::user()->user_id === $id) {
            return response()->json(
                [
                    'status' => 'error',
                    'message' => 'You are not authorized to access this resource.'
                ],
                403
            );
        } else {
            $user = User::find($id);
            if (!$user) {
                return response()->json([
                    'message' => 'User not found',
                    'status' => 'error'
                ], 404);
            } else {
                // $albums = Album::where('user_id', $id)->get();
                // foreach ($albums as $album) {
                //     $album->user_id = Auth::user()->user_id;
                //     $album->save();
                // }
                // $songs = Song::where('user_id', $id)->get();
                // foreach ($songs as $song) {
                //     $song->user_id = Auth::user()->user_id;
                //     $song->save();
                // }
                // $playlists = Playlist::where('user_id', $id)->get();
                // foreach ($playlists as $playlist) {
                //     $playlist->song()->detach();
                // }
                // Playlist::where('user_id', $id)->delete();
                DB::table('personal_access_tokens')->where('tokenable_id', $id)->delete();
                $user->delete();
                return response()->json([
                    'message' => 'User deleted successfully',
                    'status' => 'success'
                ], 200);
            }
        }
    }

    public function showUserUploadAlbum($id)
    {
        $albumUploaded = DB::table('albums')
            ->leftJoin('songs', 'albums.album_id', '=', 'songs.album_id')
            ->select('albums.*', DB::raw('count(songs.song_id) as songCount'))
            ->where('albums.user_id', Auth::user()->user_id)
            ->groupBy('albums.id')
            ->get();
        return response()->json([
            'status' => 'success',
            'album' => $albumUploaded
        ]);
    }

    public function deleteSong($id)
    {
        $song = Song::find($id);
        if (!$song) {
            return response()->json([
                'message' => 'Song not found',
                'status' => 'error'
            ], 404);
        } else {
            SongArtist::where('song_id', $id)->delete();
            SongGenre::where('song_id', $id)->delete();
            PlaylistSong::where('song_id', $id)->delete();
            LikedSong::where('song_id', $id)->delete();
            @unlink(public_path('storage/upload/song_src/') . $id . '.ogg');
            $song->delete();
            return response()->json([
                'message' => 'Song deleted successfully',
                'status' => 'success'
            ], 200);
        }
    }
}
