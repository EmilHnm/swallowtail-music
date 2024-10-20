<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class StatisticController extends Controller
{
    //

    public function saveTotalPlayedDuration(Request $request) {
        // Save the total played duration to the database.
        if($value = Redis::get(Carbon::today()->timestamp . '_total_played_duration'))
        {
            Redis::set(Carbon::today()->timestamp . '_total_played_duration', $value + $request->duration);
        } else {
            Redis::set(Carbon::today()->timestamp . '_total_played_duration', $request->duration);
        }
        // Return a success message.
        return response()->json(['message' => 'Total played duration saved successfully']);
    }

    public function saveTotalSessionsDuration(Request $request) {
        // Save the total sessions duration to the database.
        if($value = Redis::get(Carbon::today()->timestamp . '_total_sessions_duration'))
        {
            Redis::set(Carbon::today()->timestamp . '_total_sessions_duration', $value + $request->duration);
        } else {
            Redis::set(Carbon::today()->timestamp . '_total_sessions_duration', $request->duration);
        }
        // Return a success message.
        return response()->json(['message' => 'Total sessions duration saved successfully']);
    }

    public function saveTotalPlayedTime(int $time = 1) {
        // Save the total played time to the database.
        if($value = Redis::get(Carbon::today()->timestamp . '_total_played_time'))
        {
            Redis::set(Carbon::today()->timestamp . '_total_played_time', $value + $time);
        } else {
            Redis::set(Carbon::today()->timestamp . '_total_played_time', $time);
        }
    }
}
