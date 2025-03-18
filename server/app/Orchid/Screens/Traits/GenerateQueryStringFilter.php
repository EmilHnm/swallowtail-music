<?php

namespace App\Orchid\Screens\Traits;

use Illuminate\Support\Arr;

trait GenerateQueryStringFilter
{
    public function generateQueryStringFilter($field, $value, $merge = true)
    {
        $queries['filter'] = (\request()->has('filter') && $merge) ? array_merge(\request()->get('filter'), [$field => $value]) : [$field => $value];
        $query_string = Arr::query($queries);
        return $query_string;
    }
}