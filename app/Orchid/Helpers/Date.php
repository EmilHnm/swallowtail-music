<?php

namespace App\Orchid\Helpers;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Date
{
    public static function local($field)
    {
        return fn(Model $d) => $d->$field->local();
    }

    public static function human($field, $local_time = true, $format = 'Y-m-d H:i:s', $empty = '- -')
    {
        return function (Model $d) use ($field, $local_time, $format, $empty) {
            if(!$d->$field){
                return $empty;
            }
            $time =  $d->$field->format($format);
            $diffForHumans = $d->$field->diffForHumans();
            return "<span title='" . $time . "'>" . $diffForHumans . "</span>";
        };
    }

    public static function filterThisMonth($field = 'created_at'): array
    {
        return [
            $field => [
                'start' => Carbon::now()->format('Y-m-1'),
                'end'   => Carbon::now()->format('Y-m-d'),
            ]
        ];
    }

    public static function filterLastMonth($field = 'created_at'): array
    {
        return [
            $field => [
                'start' => Carbon::now()->subMonth()->format('Y-m-1'),
                'end'   => Carbon::now()->subMonth()->format('Y-m-t'),
            ]
        ];
    }

    public static function filterToday($field = 'created_at'): array
    {
        return [
            $field => [
                'start' => Carbon::now()->format('Y-m-d'),
                'end'   => Carbon::now()->format('Y-m-d'),
            ]
        ];
    }

}
