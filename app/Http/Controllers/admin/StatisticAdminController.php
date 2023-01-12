<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use App\Http\Controllers\Controller;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Rap2hpoutre\LaravelLogViewer\LaravelLogViewer;

class StatisticAdminController extends Controller
{
    //

    public function getLogger(Request $request)
    {
        $itemPerPage = $request->get("itemPerPage") ?? 10;
        $page = $request->get("page") ?? 1;
        $query_type = $request->get("query_type") ?? "all";
        $query = $request->get("query") ?? "";
        $class = $request->get("class") ?? "all";
        $level = $request->get("level") ?? "all";
        try {
            $logViewer = new LaravelLogViewer();
            $collection = collect($logViewer->all());
            if ($query_type !== "all" && $query !== "") {
                $collection = $collection->filter(function ($item) use (
                    $query,
                    $query_type
                ) {
                    return str_contains(
                        strtolower($item[$query_type]),
                        strtolower($query)
                    );
                });
            }
            if ($class !== "all") {
                $collection = $collection->filter(function ($item) use (
                    $class
                ) {
                    return $item["level_class"] === $class;
                });
            }
            if ($level !== "all") {
                $collection = $collection->filter(function ($item) use (
                    $level
                ) {
                    return $item["level"] === $level;
                });
            }
            return response()->json(
                [
                    "status" => "success",
                    "logs" => $this->paginate(
                        $collection->toArray(),
                        $itemPerPage,
                        $page
                    ),
                ],
                200,
                [],
                JSON_FORCE_OBJECT
            );
        } catch (\Exception $e) {
            return response()->json([
                "status" => "error",
                "message" => $e->getMessage(),
            ]);
        }
    }

    public function paginate($items, $perPage = 5, $page = null, $options = [])
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items =
            $items instanceof Collection ? $items : Collection::make($items);
        return new LengthAwarePaginator(
            $items->forPage($page, $perPage),
            $items->count(),
            $perPage,
            $page,
            $options
        );
    }
}
