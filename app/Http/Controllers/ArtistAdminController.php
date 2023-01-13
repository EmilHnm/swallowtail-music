<?php

namespace App\Http\Controllers;

use App\Models\Artist;
use Illuminate\Http\Request;

class ArtistAdminController extends Controller
{
    //

    public function getAll(Request $request)
    {
        $query = urldecode($request->get("query"));
        $itemPerPage = $request->get("itemPerPage") ?? 10;
        $artist = Artist::withCount(["song"])
            ->where("name", "like", "%$query%")
            ->paginate($itemPerPage);
        return response()->json([
            "status" => "success",
            "artists" => $artist,
        ]);
    }

    public function show($id)
    {
        $artist = Artist::find($id);
        if (!$artist) {
            return response()->json([
                "status" => "error",
                "message" => "Artist not found",
            ]);
        }
        return response()->json([
            "status" => "success",
            "artist" => $artist,
        ]);
    }

    public function update(Request $request)
    {
        $artistData = json_decode($request->artist);
        $artist = Artist::find($artistData->artist_id);
        if (!$artist) {
            return response()->json([
                "status" => "error",
                "message" => "Artist not found",
            ]);
        }
        $artist->name = $artistData->name;
        if ($request->hasFile("image")) {
            $image = $request->file("image");
            $image_name =
                $artistData->artist_id .
                "." .
                $image->getClientOriginalExtension();
            @unlink(public_path("storage/upload/artist_image/" . $image_name));
            $image->move(
                public_path("storage/upload/artist_image"),
                $image_name
            );
            $artist->image_path = $image_name;
        }
        $artist->save();
        return response()->json([
            "status" => "success",
            "artist" => $artist,
        ]);
    }

    public function delete($id)
    {
        $artist = Artist::find($id);
        $songs = $artist
            ->song()
            ->withCount("artist")
            ->having("artist_count", "<=", 1)
            ->get();
        foreach ($songs as $song) {
            $song->display = "private";
            $song->save();
        }
        $artist->song()->detach();
        @unlink(
            public_path("storage/upload/artist_image/" . $artist->image_path)
        );
        $artist->delete();
        return response()->json([
            "status" => "success",
        ]);
    }

    public function group(Request $request)
    {
        $from_id = $request->from;
        $to_id = $request->to;
        $artist_from = Artist::find($from_id);
        $artist_to = Artist::find($to_id);
        if (!$artist_from || !$artist_to) {
            return response()->json([
                "status" => "error",
                "message" => "Artist not found",
            ]);
        }

        $songs = $artist_from->song()->get();
        foreach ($songs as $song) {
            $song->artist()->detach($artist_from->artist_id);
            if (
                !$song
                    ->artist()
                    ->where("artist_id", $artist_to->artist_id)
                    ->exists()
            ) {
                $song->artist()->attach($artist_to->artist_id);
            }
        }
        return response()->json([
            "status" => "success",
        ]);
    }

    public function getArtistSong($id, Request $request)
    {
        $query = urldecode($request->get("query"));
        $artist = Artist::find($id);
        if (!$artist) {
            return response()->json([
                "status" => "error",
                "message" => "Artist not found",
            ]);
        }
        $songs = $artist
            ->song()
            ->with(["user", "album"])
            ->where("title", "like", "%" . $query . "%")
            ->paginate(10);
        return response()->json([
            "status" => "success",
            "songs" => $songs,
        ]);
    }
}
