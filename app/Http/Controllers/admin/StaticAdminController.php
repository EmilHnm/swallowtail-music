<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use App\Http\Controllers\Controller;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Rap2hpoutre\LaravelLogViewer\LogViewerController;

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
