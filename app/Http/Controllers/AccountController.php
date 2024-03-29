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
    public function updateAccount(Request $request): Response
    {
        $credentials = $request->validate([
            "username" => "required|string",
            "email" =>
                "required|string|email:strict|unique:users,email," .
                Auth::user()->id,
            "gender" => "required|string",
            "dob" => "required|string",
            "region" => "required|string",
            "isUpdateDisabled" => "required",
        ]);
        $authUser = Auth::user();
        $user = User::where("user_id", $authUser->user_id)->first();
        $user->name = $credentials["username"];
        $user->gender = $credentials["gender"];
        if (!$credentials["isUpdateDisabled"]) {
            $user->dob = $credentials["dob"];
        }
        $user->region = $credentials["region"];
        $user->save();
        return response(Response::HTTP_OK);
    }

    public function updatePassword(Request $request)
    {
        $validateData = $request->validate([
            "current_password" => "required",
            "password" => "required|string|confirmed",
        ]);
        $authUser = Auth::user();
        $user = User::where("user_id", $authUser->user_id)->first();
        if (!Hash::check($request->current_password, $authUser->password)) {
            return response(
                [
                    "status" => "error",
                    "message" => "The old password is not correct",
                ],
                422
            );
        }
        $user->password = bcrypt($request->password);
        $user->save();
        return response(
            [
                "status" => "success",
                "message" => "Password was saved successfully",
            ],
            Response::HTTP_OK
        );
    }

    public function updateProfilePicture(Request $requets)
    {
        $validateData = $requets->validate([
            "profile_image" => "required|image",
        ]);
        if ($requets->file("profile_image")) {
            $imageFile = $requets->file("profile_image");
            $fileName = file_path_helper(Auth::user()->id) . Auth::user()->user_id . ".jpg";
            \Storage::disk('profile_image')->put($fileName, file_get_contents($imageFile));
            $user = User::where("user_id", Auth::user()->user_id)->first();
            $user->profile_photo_url = $fileName;
            $user->save();
            return response()->json([
                "status" => "success",
                "message" => "Profile image updated successfully",
            ]);
        }
        return response(
            json_encode([
                "status" => "error",
                "message" => "Profile image not updated",
            ]),
            Response::HTTP_OK
        );
    }
}
