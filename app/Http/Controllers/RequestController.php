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
            'type' => 'required|string|in:artist,genre,song,album',
            'name' => 'required|string',
            'description' => 'required|string',
        ]);
        $client_request = new ClientRequest;
        $client_request->requester = Auth::user()->user_id;
        $client_request->type = $request->type;
        $client_request->status = RequestStatusEnum::PENDING;
        $client_request->body = [
            'name' => $request->name,
            'description' => $request->description,
        ];
        if ($client_request->type === 'artist' || $client_request->type === 'genre') {
            $client_request->user_fillable = false;
        }
        $client_request->save();

        return response()->json([
            'message' => 'Request created successfully',
            'status' => 'success',
        ], 201);
    }

    public function index(Request $request)
    {
        $requests = ClientRequest::query();
        if ($request->has('type')) {
            $requests->where('type', $request->type);
        }
        if ($request->has('status')) {
            $requests->where('status', $request->status);
        }
        if ($request->boolean('my_requests')) {
            $requests->where('requester', Auth::user()->user_id);
        }
        if ($request->has('q')) {
            $requests->where('body->name', 'like', '%' . $request->q . '%');
            $requests->orWhere('body->description', 'like', '%' . $request->q . '%');
        }
        $requests = $requests->orderBy('created_at', 'desc')->paginate($request->per_page ?? 10);

        return response()->json([
            'requests' => $requests,
            'status' => 'success',
        ], 200);
    }

    public function cancel($id)
    {
        $request = ClientRequest::find($id);
        if (!$request) {
            return response()->json([
                'message' => 'Request not found',
                'status' => 'error',
            ], 404);
        }
        if ($request->requester !== Auth::user()->user_id) {
            return response()->json([
                'message' => 'You are not authorized to cancel this request',
                'status' => 'error',
            ], 403);
        }
        if ($request->status !== RequestStatusEnum::PENDING) {
            return response()->json([
                'message' => 'Request is not pending',
                'status' => 'error',
            ], 403);
        }
        $request->status = RequestStatusEnum::CANCELLED;
        $request->save();

        return response()->json([
            'message' => 'Request cancelled successfully',
            'status' => 'success',
        ], 200);
    }


    public function show($id)
    {
        $request = ClientRequest::find($id);
        if (!$request) {
            return response()->json([
                'message' => 'Request not found',
                'status' => 'error',
            ], 404);
        }
        return response()->json([
            'request' => $request,
            'status' => 'success',
        ], 200);
    }
}
