<?php

namespace App\Http\Controllers;

use App\Enum\RequestStatusEnum;
use Illuminate\Http\Request;
use App\Models\Request as ClientRequest;
use Auth;

class RequestController extends Controller
{
    //
    public function create(Request $request)
    {
        $request->validate([
            'request_type' => 'required|string|in:artist,genre,song,album',
            'name' => 'required|string',
            'description' => 'required|string',
        ]);
        $client_request = new ClientRequest;
        $client_request->requester = Auth::user()->user_id;
        $client_request->request_type = $request->request_type;
        $client_request->request_status = RequestStatusEnum::PENDING;
        $client_request->request_description = json_encode([
            'name' => $request->name,
            'description' => $request->description,
        ]);
        if ($client_request->request_type === 'artist' || $client_request->request_type === 'genre') {
            $client_request->user_fillable = false;
        }
        $client_request->save();

        return response()->json([
            'message' => 'Request created successfully',
            'status' => 'success',
        ], 201);
    }
}
