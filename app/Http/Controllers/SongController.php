<?php

namespace App\Http\Controllers;

use App\Models\Song;
use App\Enum\RefererEnum;
use App\Models\LikedSong;
use App\Models\SongGenre;
use App\Models\SongArtist;
use Illuminate\Support\Str;
use App\Models\PlaylistSong;
use App\Models\SongMetadata;
use App\Orchid\Helpers\Text;
use Illuminate\Http\Request;
use App\Services\SongManager;
use App\Jobs\ProcessSongConvert;
use App\Enum\SongMetadataStatusEnum;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SongController extends Controller
{
    //

    public function uploadSong(Request $request)
    {
        $artist = json_decode($request->artist);
        $genre = json_decode($request->genre);

        $song = new Song();
        $song->song_id = "song_" . date("YmdHi") . Str::random(10);
        $song->user_id = Auth::user()->user_id;
        $song->title = $request->songName;
        $song->display = $request->displayMode;

        foreach ($artist as $id) {
            $song_artist = new SongArtist();
            $song_artist->song_id = $song->song_id;
            $song_artist->artist_id = $id;
            $song_artist->save();
        }

        foreach ($genre as $id) {
            $song_genre = new SongGenre();
            $song_genre->song_id = $song->song_id;
            $song_genre->genre_id = $id;
            $song_genre->save();
        }

        $song->save();
        return
            response()->json([
                "status" => "success",
                "message" =>
                "Song information uploaded successfully. The song will ready in some minutes. Please do not close tab until the song is uploaded.",
                "song_id" => $song->song_id,
            ]);
    }

    public function uploadSongFile(Request $request, $id)
    {
        $file = $request->file('file');

        $path = Storage::disk('chunk_file')->path("$id/{$request->file('file')->getClientOriginalName()}");
        if (Storage::disk('chunk_file')->exists("$id/{$file->getClientOriginalName()}")) {
            // Storage::disk($disk)->append("chunks/{$file->getClientOriginalName()}", $file->get());
            \File::append($path, $file->get());
            if (!SongMetadata::where("song_id", $id)->exists()) {
                $song_file = new SongMetadata();
                $song_file->song_id = $id;
                $song_file->status = SongMetadataStatusEnum::UPLOADING;
                $song_file->referer = RefererEnum::USER;
                $song_file->save();
            }
        } else {
            Storage::disk('chunk_file')->put("$id/{$file->getClientOriginalName()}", $file->get());
            // \File::put($path, $file->get());
        }

        if ($request->has('is_last') && $request->boolean('is_last')) {
            $name_final = str_replace(".part", "", $file->getClientOriginalName());
            if (Storage::disk("raws_audio")->exists($name_final)) {
                Storage::disk('raws_audio')->delete($name_final);
            }
            Storage::disk('raws_audio')->writeStream(
                $name_final,
                Storage::disk('chunk_file')->readStream("$id/{$file->getClientOriginalName()}")
            );
            Storage::disk('chunk_file')->deleteDirectory($id);
            $song = Song::find($id);
            $song->file->status = SongMetadataStatusEnum::UPLOADED;
            $song->file->driver = 'raws_audio';
            $song->save();
            $song->file->save();
            //            dispatch(new ProcessSongConvert(Song::find($id), $disk, $name_final));
            ProcessSongConvert::dispatchSync(Song::find($id), 'raws_audio', $name_final);
            return response()->json(['uploaded' => true]);
        }
    }

    public function updateSong(Request $request)
    {
        $artist = json_decode($request->artist);
        $genre = json_decode($request->genre);
        $song = Song::find($request->song_id);
        if ($song->user_id != Auth::user()->user_id) {
            return response()->json([
                "status" => "error",
                "message" => "You are not allowed to update this song.",
            ]);
        }
        $song->title = $request->songName;
        $song->display = $request->displayMode;
        $song->save();
        $song->artist()->detach();
        $song->genre()->detach();
        $song->artist()->attach($artist);
        $song->genre()->attach($genre);
        $song->searchable();
        return response()->json([
            "status" => "success",
            "message" => "Song Updated Successfully",
        ]);
    }

    public function streamSong($id)
    {
        $song = Song::where("song_id", $id)->first();
        if (!$song) {
            return response()->json([
                "status" => "error",
                "message" => "Song not found",
            ]);
        }
        if (
            Auth::user()->user_id != $song->user_id &&
            $song->display == "private"
        ) {
            return response()->json([
                "status" => "error",
                "message" => "You are not allowed to stream this song.",
            ]);
        }
        $url = (new SongManager($song))->stream();
        return  $url;
    }

    public function uploadedSong(Request $request)
    {
        $songs = Song::with(['artist'])->where("user_id", Auth::user()->user_id);

        if ($request->query->has("query")) {
            $query = $request->query->get("query");
            $songs = $songs->where("title", "like", "%" . $query . "%")
                ->orWhere("normalized_title", "like", "%" . $query . "%");
        }

        if ($request->query->has("sort") && $request->query->has("order")) {
            $sort = $request->query->get("sort");
            $order = $request->query->get("order");
            if (
                in_array($sort, ['title', 'normalized_title', 'listens', 'created_at'])
                && in_array($order, ['asc', 'desc'])
            ) {
                $songs = $songs->orderBy($sort, $order);
            }
        }

        return response()->json([
            "status" => "success",
            "songs" => $songs->paginate(),
        ]);
    }

    public function getSongInfo($id)
    {
        $song = Song::where("song_id", $id)->first();
        if (!$song->id) {
            return response()->json([
                "status" => "error",
                "message" => "Song not found",
            ]);
        }
        if (
            Auth::user()->user_id != $song->user_id &&
            $song->display == "private"
        ) {
            return response()->json(
                [
                    "status" => "error",
                    "message" =>
                    "You are not authorized to access this resource.",
                ],
                403
            );
        }
        $genre = $song
            ->genre()
            ->where("song_id", $id)
            ->get();
        $artist = $song
            ->artist()
            ->where("song_id", $id)
            ->get();
        return response()->json([
            "status" => "success",
            "song" => $song,
            "genre" => $genre,
            "artist" => $artist,
        ]);
    }

    public function getSongForPlay($id)
    {
        $song = Song::where("song_id", $id)
            ->with(["artist", "album", "like"])
            ->first();
        return response()->json([
            "status" => "success",
            "song" => $song,
        ]);
    }

    public function increaseSongListens($id, Request $request)
    {
        $song = Song::find($id);
        $song->increment('listens');
        event(new \App\Events\ListenIncrease($song));
        return response()->json([
            "status" => "success",
            "message" => "Song listens increased",
        ]);
    }

    public function likeSong($id)
    {
        $song = Song::where("song_id", $id)->first();
        if (!$song->id) {
            return response()->json([
                "status" => "error",
                "message" => "Song not found",
            ]);
        }

        $liked = LikedSong::where("song_id", $id)
            ->where("user_id", Auth::user()->user_id)
            ->first();
        if ($liked) {
            $liked->delete();
            return response()->json([
                "status" => "success",
                "message" => "Song unliked successfully",
            ]);
        } else {
            $liked = new LikedSong();
            $liked->song_id = $id;
            $liked->user_id = Auth::user()->user_id;
            $liked->save();
            return response()->json([
                "status" => "success",
                "message" => "Song liked successfully",
            ]);
        }
    }

    public function likedSong($id)
    {
        $song = Song::where("song_id", $id)->first();
        if (!$song->id) {
            return response()->json([
                "status" => "error",
                "message" => "Song not found",
            ]);
        }

        $liked = LikedSong::where("song_id", $id)
            ->where("user_id", Auth::user()->user_id)
            ->first();
        if ($liked) {
            return response()->json([
                "status" => "success",
                "liked" => true,
            ]);
        } else {
            return response()->json([
                "status" => "success",
                "liked" => false,
            ]);
        }
    }

    public function deleteSong($id)
    {
        $song = Song::where("song_id", $id)->first();
        if (!$song->id) {
            return response()->json([
                "status" => "error",
                "message" => "Song not found",
            ]);
        }
        if ($song->user_id != Auth::user()->user_id) {
            return response()->json([
                "status" => "error",
                "message" => "You are not allowed to delete this song!",
            ]);
        }
        SongArtist::where("song_id", $id)->delete();
        SongGenre::where("song_id", $id)->delete();
        PlaylistSong::where("song_id", $id)->delete();
        LikedSong::where("song_id", $id)->delete();
        $songManager = new SongManager($song);
        $songManager->removeFile();
        $song->delete();
        return response()->json([
            "status" => "success",
            "message" => "Song deleted successfully",
        ]);
    }

    public function latestSong()
    {
        $songs = Song::with(["artist", "like", "album"])
            ->where("display", "public")
            ->whereHas("file", fn ($q) => $q->where("status", SongMetadataStatusEnum::DONE))
            ->orderBy("created_at", "DESC")
            ->limit(5)
            ->get();
        return response()->json([
            "status" => "success",
            "songs" => $songs,
        ]);
    }

    public function songLyrics($id)
    {
        $lyrics = SongMetadata::where("song_id", $id)->first()?->getLyricData() ?? [];
        return response()->json([
            "status" => "success",
            "lyrics" => $lyrics,
        ]);
    }

    public function searchSong(Request $request)
    {
        if ($request->query->has("query")) {
            $query = Text::normalize($request->query->get("query"));

            try {
                $songs = Song::search("title:($query~1)^3 or normalized_title:($query~1)^2 or ($query) or album.name:($query~1) or artist.name:($query~1)")
                    ->where("display", "public")
                    ->take(10)
                    ->get()->load(["artist", "album", "like"]);
            } catch (\Throwable $th) {
                $songs = Song::with(["artist", "album", "like"])
                    ->where("title", "like", "%" . $query . "%")
                    ->orWhere("normalized_title", "like", "%" . $query . "%")
                    ->where("display", "public")
                    ->having("artist_count", '>=', '1')
                    ->limit(10)
                    ->get();
            }

            return response()->json([
                "status" => "success",
                "songs" => $songs,
            ]);
        } else {
            return response()->json([
                "status" => "error",
                "message" => "Query not found",
            ]);
        }
    }
}
