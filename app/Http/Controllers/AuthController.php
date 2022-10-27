<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    //
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email|string|exists:users,email',
            'password' => [
                'required',
            ],
            'remember_me' => 'boolean'
        ]);
        if (!Auth::attempt($credentials)) {
            return response([
                'error' => 'The Provided credentials are not correct'
            ], 422);
        }
        $user = Auth::user();
        $token = $user->createToken('main')->plainTextToken;

        return response([
            'user' => $user,
            'token' => $token
        ]);
    }
    public function logout(Request $request)
    {
        $user = Auth::user();
        $user->currentAccessToken()->delete();
        return response([
            'message' => 'Logged out'
        ], Response::HTTP_OK);
    }
    public function logoutAllDevice(Request $request)
    {
        $user = Auth::user();
        $user->tokens()->delete();
        return response([
            'status' => 'success',
            'message' => 'Logged out from all devices'
        ], Response::HTTP_OK);
    }
    public function register(Request $request)
    {
        $validateData = $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|confirmed'
        ]);

        // $user = User::create([
        //     'name' => $validateData['name'],
        //     'email' => $validateData['email'],
        //     'user_id' => Carbon::now()->timestamp,
        //     'password' => bcrypt($validateData['password'])
        // ]);

        $user = new User();
        $user->name = $validateData['name'];
        $user->email = $validateData['email'];
        $user->user_id = Carbon::now()->timestamp;
        $user->password = bcrypt($validateData['password']);
        $user->save();

        $token = $user->createToken('main')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => $user
        ], Response::HTTP_OK);
    }

    public function user(Request $request)
    {
        $user = Auth::user();
        return response()->json($user);
    }
}
