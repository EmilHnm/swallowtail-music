<?php

namespace App\Services;

trait NeedCachedService
{
    public function __call(string $name, array $arguments)
    {
        if($result = \Cache::get($name)) {
            return $result;
        } else {
            $result = call_user_func_array([$this, $name], $arguments);
            \Cache::put($name, $result, 60);
            return $result;
        }
    }
}
