<?php

namespace App\Orchid\Layouts;

use App\Enum\RequestStatusEnum;
use App\Enum\ResponseStatusEnum;
use App\Models\Request;
use App\Models\Response;
use Orchid\Screen\Components\Cells\DateTimeSplit;
use Orchid\Screen\Layout;
use Orchid\Screen\Layouts\Modal;
use Orchid\Screen\Repository;
use Orchid\Screen\Sight;
use Orchid\Screen\TD;

class RequestDetailsModal extends Modal
{
    public function __construct(string $key, array $layouts = [], $target = 'model')
    {
        $layouts = [
            new class($target) extends Layout {
                public function __construct(protected $target)
                {
                }

                public function build(Repository $repository)
                {
                    $data = $repository->getContent('data');
                    if ($data) {
                        return \Orchid\Support\Facades\Layout::blank([
                            \Orchid\Support\Facades\Layout::legend('data', [
                                Sight::make('id'),
                                Sight::make('type'),
                                Sight::make('body.name', 'Name'),
                                Sight::make('body.description', 'Description'),
                                Sight::make('status')->render(fn(Request $request) => RequestStatusEnum::search($request->status)),
                                Sight::make('user_fillable', 'User Fillable')->render(fn(Request $request) => $request->user_fillable ? "Yes" : "No"),
                                Sight::make('filled_by', 'Filled By')->render(fn(Request $request) => $request->filledBy?->name ?? 'N/A'),
                                Sight::make('created_at', 'Created at')->asComponent(DateTimeSplit::class),
                                Sight::make('updated_at', 'Updated at')->asComponent(DateTimeSplit::class),
                            ]),
                            \Orchid\Support\Facades\Layout::table('data.responses', [
                                TD::make('responder', 'Responder')->render(fn(Response $response) => $response->responder),
                                TD::make('content', 'Content'),
                                TD::make('status')->render(fn(Response $response) => ResponseStatusEnum::search($response->status)),
                                TD::make('created_at', 'Created at')->asComponent(DateTimeSplit::class),
                            ])->title('Responses'),
                        ])->build($repository);
                    }
                    return view('vendor.platform.dump', ['data' => "Loading..."]);
                }
            },
        ];
        parent::__construct($key, $layouts);
    }
}
