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

    public function get($id)
    {
        $artist = Artist::find($id);
        return response()->json([
            "status" => "success",
            "artist" => $artist,
        ]);
    }

    public function delete($id)
    {
        $artist = Artist::find($id);
        $artist->song()->update(["display" => "private"]);
        $artist->song()->detach();
        $artist->delete();
        return response()->json([
            "status" => "success",
        ]);
    }

    public function group($id)
    {
        return response()->json([
            "status" => "success",
        ]);
    }
}
