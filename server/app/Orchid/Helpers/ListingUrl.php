<?php

namespace App\Orchid\Helpers;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Orchid\Screen\Actions\Link;

class ListingUrl
{
    public static function make(array $filter, string $sort = null, $merge = true, $revert_sort = false): string
    {
        $request = \Request::capture();
        $current_filter = $merge ? $request->get('filter', []) : [];
        $filter = array_merge($current_filter, $filter);
        if ($revert_sort && !$sort) {
            $sorting = $request->get('sort');
            if (Str::startsWith($sorting, "-")) {
                $sort = mb_substr($sorting, 1);
            } else {
                $sort = "-" . $sorting;
            }
        }

        return $request->fullUrlWithQuery(['sort' => $sort, 'filter' => $filter]);
    }

    public static function cell($field, $sort = '', $text_field = null)
    {
        return function ($record) use ($field, $sort, $text_field) {
            return
                Link::make(Arr::get($record,$text_field ?: $field))
                    ->href(
                        self::make(
                            [$field => $record->$field], $sort
                        )
                    );
        };
    }
}