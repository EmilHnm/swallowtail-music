<?php


namespace App\Models\Traits;


use App\Libs\System\DB\CrDB;
use App\Orchid\Helpers\Common;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

trait AdvancedFilters
{
    public function scopeIdRange(Builder $query, $id, $field = 'id')
    {
        return $query->when($id, function (Builder $query) use ($id, $field) {
            if (strpos($id, '-')) {
                [$min, $max] = explode("-", $id);
                $query->where($field, ">=", $min);
                $query->where($field, "<=", $max);
            } elseif (strpos($id, ',')) {
                $ids = explode(',', $id);
                $query->whereIn($field, $ids);
            } else {
                $query->where($field, $id);
            }
        });
    }

    public function scopeAdvancedFilter($query, $filter_keys = [], $allowed_sorts = [])
    {
        $filter = \Request::get('filter', []);
        foreach ($filter_keys as $key) {

            if (is_array($key)) {
                [$key, $callable] = $key;
                $value = \Arr::get($filter, $key, null);

                if ($value !== null) {
                    if (is_string($callable)) {
                        $this->$callable($value);
                    } elseif (is_callable($callable)) {
                        call_user_func($callable, $query, $value, $key);
                    }
                }
                continue;
            }
            if (is_array(\Arr::get($filter, $key))) {
                $array_filters = \Arr::get($filter, $key);
                if (array_key_exists('min', $array_filters)) {
                    $min = $array_filters['min'];
                    $query->where($key, '>=', $min);
                }
                if (array_key_exists('max', $array_filters)) {
                    $max = $array_filters['max'];
                    $query->where($key, '<=', $max);
                }
                continue;
            }

            [$key, $type] = explode(":", $key . ":");
            $value = \Arr::get($filter, $key);
            if ($value == 'unset') {
                $query->whereNull($key);
                continue;
            }
            if ($value === null) {
                continue;
            }
            switch ($type) {
                case "int":
                    if (is_array($value)) {
                        if (isset($value['min'])) {
                            $query->where($key, '>=', (int)$value['min']);
                        }
                        if (isset($value['max'])) {
                            $query->where($key, '<=', (int)$value['max']);
                        }
                    } else {
                        $values = explode(",", $value);
                        if (count($values) == 1) {
                            $query->where($key, $values);
                        } else {
                            $query->whereIn($key, $values);
                        }
                    }
                    break;
                case "prefix":
                    $query->where($key, 'like', $value . "%");
                    break;
                case ">":
                case "<":
                case "<>":
                    $query->where($key, $type, $value);
                    break;
                case "date_range":
                    if ($start = $value['start']) {
                        $start = Carbon::parse($start, 'UTC');
                        $query->where($key, '>=', $start->format('Y-m-d 00:00:00'));
                    }
                    if ($end = $value['end']) {
                        $end = Carbon::parse($end, 'UTC');
                        $query->where($key, '<=', $end->format('Y-m-d 23:59:59'));
                    }
                    break;
                case "date_range_tz":
                    if ($start = $value['start']) {
                        $start = Carbon::parse($start, 'UTC');
                        $query->whereDate($key, '>=', $start);
                    }
                    if ($end = $value['end']) {
                        $end = Carbon::parse($end, 'UTC');
                        $query->whereDate($key, '<=', $end);
                    }
                    break;
                default:
                    $query->where($key, $value);
            }
        }

        $query_sorts = collect(\Request::get('sort', []));
        $allowed_sorts = collect($allowed_sorts);
        $this->addSortsByToQuery($query, $query_sorts, $allowed_sorts);

        return $query;
    }

    public function scopeDefaultSortBy(Builder $builder, string $column, string $direction = 'asc')
    {
        if (empty($builder->getQuery()->orders)) {
            $builder->orderBy($column, $direction);
        }

        return $builder;
    }

    /**
     * @param Builder $builder
     */
    protected function addSortsByToQuery(Builder $builder, $query_sorts, $allowed_sorts)
    {
        $sortJson  = [];
        $allowed_sorts->each(function ($sort) use (&$sortJson){
            if(strpos($sort, ':'))
            {
                [$key, $value] = explode(':', $sort);
                $sortJson[$key] = $value;
            }
        });
        $query_sorts
            ->each(function (string $sort) use ($builder, $allowed_sorts, $sortJson) {
                $descending = str_starts_with($sort, '-');
                $key = ltrim($sort, '-');

                if ($allowed_sorts->contains($key)) {
                    $key = Common::sanitize($key);
                    $builder->orderBy($key, $descending ? 'desc' : 'asc');
                }
                elseif(array_key_exists($key, $sortJson)){
                    $key = Common::sanitize($key);
                    $builder->orderBy("{$sortJson[$key]}->$key", $descending ? 'desc' : 'asc');
                }
            });
    }

}
