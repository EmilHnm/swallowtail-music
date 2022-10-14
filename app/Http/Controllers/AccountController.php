<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AccountController extends Controller
{
    //
    public function getAccount()
    {
        $user = Auth::user();
        return response([
            'user' => $user
        ], Response::HTTP_OK);
    }

    public function saveAccount(Request $request)
    {
        $validateData = $request->validate([
            'name' => 'required|string',
            'gender' => 'required|string',
            'dob' => 'required|string',
            'region' => 'required|string',
        ]);
        $authUser = Auth::user();
        $user = User::where('user_id', $authUser->user_id)->first();
        $user->name = $validateData['name'];
        $user->gender = $validateData['gender'];
        $user->dob = $validateData['dob'];
        $user->region = $validateData['region'];
        $user->save();
        return response(Response::HTTP_OK);
    }

    public function updatePassword(Request $request)
    {
        $validateData = $request->validate([
            'old_password' => 'required',
            'password' => 'required|string|confirmed'
        ]);
        $authUser = Auth::user();
        $user = User::where('user_id', $authUser->user_id)->first();
        if (!Hash::check($request->old_password, $user->password)) {
            return response([
                'error' => 'The old password is not correct'
            ], 422);
        }
        $user->password = bcrypt($request->password);
        $user->save();
        return response(Response::HTTP_OK);
    }
}
