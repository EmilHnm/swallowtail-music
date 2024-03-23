<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Symfony\Component\HttpFoundation\Response;

class CountingRequest
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $name = Carbon::today()->timestamp . '_total_requests';
        $currentCount = Redis::get($name);
        if ($currentCount) {
            Redis::set($name, $currentCount + 1);
        } else {
            Redis::set($name, 1);
        }
        return $next($request);
    }
}
