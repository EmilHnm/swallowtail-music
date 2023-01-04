<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Pagination\Paginator;
use Rap2hpoutre\LaravelLogViewer\LogViewerController;
use Illuminate\Pagination\LengthAwarePaginator;

class StaticAdminController extends Controller
{
    //

    public function getLogger(Request $request)
    {
        $logViewer = new LogViewerController();
        $logs = $logViewer->index($request);
        return response()->json([
            "status" => "success",
            "logs" => $this->paginate($logs),
        ]);
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
