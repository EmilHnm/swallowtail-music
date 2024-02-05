<?php

namespace App\Http\Controllers\admin;

use App\Models\Genre;
use App\Models\Song;
use Illuminate\Http\Request;
use Orchid\Support\Facades\Toast;

trait GenreAdminController
{

    public function asyncPassingId(Request $request)
    {
        return [
            'id' => $request->get('id'),
        ];
    }

    public function create(Request $request)
    {
        $request->validate([
            "name" => "required|string|max:255",
            "description" => "required|string|min:50",
        ]);
        $genre = new Genre();
        $genre->genre_id = "genre_" . \Str::random(10);
        $genre->name = $request->name;
        $genre->description = $request->description;
        $genre->save();
        Toast::success("Update '{$genre->name}' Genre Successfully");
        return redirect()->route('platform.classification.genres');
    }

    public function update(Request $request)
    {
        $request->validate([
            "name" => "required|string|max:255",
            "description" => "required|string|min:50",
            "genre_id" => "required|string|exists:genres,genre_id",
        ]);
        $genre = Genre::find($request->genre_id);
        $genre->name = $request->name;
        $genre->description = $request->description;
        $genre->save();
        Toast::success("Update '{$genre->name}' Genre Successfully");
        return redirect()->route('platform.classification.genres');
    }

    public function delete($id)
    {
        $genre = Genre::find($id);
        $songs = Song::whereHas("genre", function ($query) use ($id) {
            $query->where("genres.genre_id", $id);
        })->withCount('genre')
            ->having('genre_count', '=', 1)
            ->where('display', '=', 'public')
            ->get();
        foreach ($songs as $song) {
            $song->display = "private";
            $song->save();
        }
        $genre->song()->detach();
        Toast::warning("Delete '{$genre->name}' Genre Successfully");
        $genre->delete();
        return redirect()->route('platform.classification.genres');
    }

    public function group(Request $request)
    {
        $request->validate([
            "from" => "required|string|exists:genres,genre_id",
            "to" => "required|string|exists:genres,genre_id",
        ]);
        $from_id = $request->from;
        $to_id = $request->to;
        $genre_from = Genre::find($from_id);
        $genre_to = Genre::find($to_id);

        $genre_to->song()->attach($genre_from->song()->get()->pluck('song_id')->toArray());
        $genre_from->song()->detach();
        Toast::warning("Group '{$genre_from->name}' to '{$genre_to->name}' Successfully");
        $genre_from->delete();
        return redirect()->route('platform.classification.genres');
    }
}
