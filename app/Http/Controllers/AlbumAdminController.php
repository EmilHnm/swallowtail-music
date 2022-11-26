<?php

namespace App\Http\Controllers;

use App\Models\Album;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AlbumAdminController extends Controller
{
    //
    public function getAll()
    {
        if (Auth::user()->role === '') {
            return response()->json(['message' => 'You are not authorized to access this resource.'], 403);
        } else {
            $albums = Album::with(["user"])
                ->withCount(["song"])
                ->get();
            return response()->json([
                'albums' => $albums,
                'status' => "success"
            ], 200);
        }
    }
}
