<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class RedisController extends Controller
{
    //
    public function index()
    {
        $redis = Redis::connection();
        $redis->set('name', 'Taylor');
        $value = $redis->get('name');
        return $value;
    }
}
