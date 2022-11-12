<?php

namespace App\Http\Controllers;

use App\Models\Artist;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class ArtistController extends Controller
{
    //

    public function getAll()
    {
        $listArtist = Artist::all();
        return response()->json($listArtist, Response::HTTP_OK);
    }

    public function show($id)
    {
        $artist = DB::table('artists')
            ->leftJoin('song_artists', 'artists.artist_id', '=', 'song_artists.artist_id')
            ->leftJoin('songs', 'song_artists.song_id', '=', 'songs.song_id')
            ->select('artists.*', DB::raw('sum(songs.listens) as total_listens'), DB::raw('count(distinct songs.album_id) as total_album'))
            ->where('artists.artist_id', $id)
            ->groupBy('artists.artist_id')
            ->first();
        return response()->json([
            'status' => 'success',
            'artist' => $artist
        ], Response::HTTP_OK);
    }

    public function searchArtist(Request $request)
    {
        if ($request->query->has('query')) {
            $query = $request->query->get('query');
            $artists = Artist::where('name', 'like', '%' . $query . '%')->get();
            return response()->json([
                'status' => 'success',
                'artists' => $artists
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Query not found'
            ]);
        }
    }

    public function getTop()
    {
        $artist = DB::table('artists')
            ->leftJoin('song_artists', 'artists.artist_id', '=', 'song_artists.artist_id')
            ->leftJoin('songs', 'song_artists.song_id', '=', 'songs.song_id')
            ->select('artists.*', DB::raw('sum(songs.listens) as total'))
            ->groupBy('artists.artist_id')
            ->limit(8)
            ->get();
        return response()->json([
            'status' => 'success',
            'artists' => $artist
        ]);
    }

    public function getBySongByArtistId($id)
    {
        $artist = DB::table('artists')
            ->join('song_artists', 'artists.artist_id', '=', 'song_artists.artist_id')
            ->join('songs', 'song_artists.song_id', '=', 'songs.song_id')
            ->select('artists.*')
            ->where('songs.song_id', $id)
            ->get();
        return response()->json([
            'status' => 'success',
            'artists' => $artist
        ]);
    }

    public function getTopSongByArtistId($id)
    {
        $songs = DB::table('songs')
            ->join('song_artists', 'songs.song_id', '=', 'song_artists.song_id')
            ->join('artists', 'song_artists.artist_id', '=', 'artists.artist_id')
            ->join('albums', 'songs.album_id', '=', 'albums.album_id')
            ->leftJoin('liked_songs', 'songs.song_id', '=', 'liked_songs.song_id')
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
                DB::raw('CASE WHEN liked_songs.song_id != "" THEN 1 ELSE 0 END as liked')
            )
            ->join(
                DB::raw('(select `song_artists`.`song_id`
                        from song_artists
                        inner join songs on songs.song_id = song_artists.song_id
                        where artist_id = ?
                        group by `songs`.`song_id`
                        order by songs.listens desc
                        limit 10
                        ) as song_temp'),
                function ($join) {
                    $join->on('song_temp.song_id', '=', 'songs.song_id');
                }
            )
            ->orderBy('songs.listens', 'desc')
            ->setBindings([$id])
            ->get();
        return response()->json([
            'status' => 'success',
            'songs' => $songs
        ]);
    }

    public function getAlbumByArtistId($id)
    {
        $albums = DB::table('songs')
            ->join('albums', 'songs.album_id', '=', 'albums.album_id')
            ->join('song_artists', 'songs.song_id', '=', 'song_artists.song_id')
            ->join('artists', 'song_artists.artist_id', '=', 'artists.artist_id')
            ->groupBy('albums.album_id')
            ->select('albums.*', DB::raw('count(albums.album_id) as song_count'))
            ->where('artists.artist_id', $id)
            ->get();
        return response()->json([
            'status' => 'success',
            'albums' => $albums
        ]);
    }
}
