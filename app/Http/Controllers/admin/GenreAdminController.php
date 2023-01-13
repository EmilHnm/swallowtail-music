<?php

namespace App\Http\Controllers\admin;

use App\Models\Genre;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class GenreAdminController extends Controller
{
    //
    public function getAll(Request $request)
    {
        $query = urldecode($request->get("query"));
        $perPage = $request->get("itemPerPage") ?? 10;
        $genres = Genre::withCount(["song"])
            ->where("name", "like", "%" . $query . "%")
            ->paginate($perPage, ["id", "genre_id", "name"]);
        return response()->json([
            "status" => "success",
            "genres" => $genres,
        ]);
    }

    public function get($id)
    {
        $genre = Genre::find($id);
        return response()->json([
            "status" => "success",
            "genre" => $genre,
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            "name" => "required|string|max:255",
            "description" => "required|string|min:100",
            "genre_id" => "required|string|exists:genres,genre_id",
        ]);
        $genre = Genre::find($request->genre_id);
        $genre->name = $request->name;
        $genre->description = $request->description;
        $genre->save();
        return response()->json([
            "status" => "success",
            "genre" => "Update Gerne Successfully",
        ]);
    }

    public function delete($id)
    {
        $genre = Genre::find($id);
        $songs = $genre
            ->song()
            ->withCount("artist")
            ->having("artist_count", "<=", 1)
            ->get();
        foreach ($songs as $song) {
            $song->display = "private";
            $song->save();
        }
        $genre->song()->detach();
        $genre->delete();
        return response()->json([
            "status" => "success",
        ]);
    }

    public function getSong($id, Request $request)
    {
        $query = urldecode($request->get("query"));
        $itemPerPage = $request->get("itemPerPage") ?? 10;
        $genre = Genre::find($id);
        $songs = $genre
            ->song()
            ->where("title", "LIKE", "%" . $query . "%")
            ->orWhere("sub_title", "LIKE", "%" . $query . "%")
            ->paginate($itemPerPage);
        return response()->json([
            "status" => "success",
            "songs" => $songs,
        ]);
    }

    public function group(Request $request)
    {
        $request->validate([
            "from" => "required|string|exists:genres,genre_id",
            "to" => "required|string|exists:genres,genre_id",
        ]);
        $from_id = $request->from;
        $to_id = $request->to;
        if ($from_id == $to_id) {
            return response()->json([
                "status" => "error",
                "message" => "Genre not found",
            ]);
        }
        $genre_from = Genre::find($from_id);
        $genre_to = Genre::find($to_id);

        $songs = $genre_from->song()->get();
        foreach ($songs as $song) {
            $song->genre()->detach($genre_from->genre_id);
            if (
                !$song
                    ->genre()
                    ->where("genre_id", $genre_to->genre_id)
                    ->exists()
            ) {
                $song->genre()->attach($genre_to->genre_id);
            }
        }
        return response()->json([
            "status" => "success",
        ]);
    }
}
