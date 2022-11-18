<?php

namespace App\Http\Middleware;

use Closure;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class IncreaseListenTimeDelay
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $reqTime = Redis::get($request->bearerToken());
        if ($reqTime) {
            $timelast = date($reqTime);
            $timenow = date(Carbon::now()->timestamp);
            $diff = $timenow - $timelast;
            if ($diff < 45) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'You are not allowed to increase listens'
                ]);
            }
        }
        Redis::set($request->bearerToken(),  Carbon::now()->timestamp);
        return $next($request);
    }
}
