<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;

class AuthController extends Controller
{
    //
    public function login(Request $request)
    {
        $validateData = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);
    }
    public function logout(Request $request)
    {
    }
    public function register(Request $request)
    {
        $validateData = $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|confirmed'
        ]);
        $user = User::create([
            'id' => DateTime()->format(DateTime::ATOM),
            'name' => $validateData['name'],
            'email' => $validateData['email'],
            'password' => bcrypt($validateData['password'])
        ]);
        $token = $user->createToken('main')->plainTextToken;
        return response()->json([
            'token' => $token,
            'user' => $user
        ]);
    }
}
