<?php

namespace App\Orchid\Screens\Queue;

use App\Models\Queue\QueueFailedJob;
use App\Orchid\Helpers\Date;
use App\Orchid\Screens\Traits\GenerateQueryStringFilter;
use App\Orchid\Screens\Traits\HasDumpModelModal;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Screen;
use Orchid\Screen\TD;
use Orchid\Support\Facades\Layout;

class FailedJobListScreen extends Screen
{
    use GenerateQueryStringFilter, HasDumpModelModal;

    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        $queue = QueueFailedJob::query()
            ->advancedFilter([
                'id',
                'connection',
                'queue',

            ],
                [
                    'id',
                    'failed_at',
                ]);

        return [
            'jobs' => $queue->paginate(),
        ];
    }

    /**
     * The name of the screen displayed in the header.
     */
    public function name(): ?string
    {
        return 'FailedJobListScreen';
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            Link::make('Monitor job')->route('platform.queue.monitor'),
            Link::make(' Jobs')->route('platform.queue.jobs'),

        ];
    }

    /**
     * The screen's layout elements.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout(): iterable
    {
        return [
            $this->getDumpModal(),
            Layout::modal('showException',
                [
                    Layout::view('vendor.platform.dump_exception'),
                ])
                ->async('asyncGetData')
                ->size('modal-xl')
                ->withoutApplyButton()
                ->title('Exception'),
            Layout::table('jobs', [
                TD::make('id')->render(fn (QueueFailedJob $job) => $job->id.self::getDumpModelToggle(QueueFailedJob::class, $job->id)),
                TD::make('connection'),
                TD::make('queue'),
                TD::make('payload')
                    ->render(fn (QueueFailedJob $job) => ModalToggle::make('Payload')
                        ->modal('showData')
                        ->async('asyncGetData')
                        ->asyncParameters([
                            'id' => (string) $job->id,
                            'model' => QueueFailedJob::class,
                            'title' => 'Payload',
                            'property' => 'payload',
                        ])),

                TD::make('exception')
                    ->render(fn (QueueFailedJob $job) => ModalToggle::make('Error')
                        ->modal('showException')
                        ->async('asyncGetData')
                        ->asyncParameters([
                            'id' => (string) $job->id,
                        ])),
                TD::make('failed_at')
                    ->sort()
                    ->render(Date::human('failed_at')),
            ]),
        ];
    }

    public function asyncGetData(Request $request)
    {
        $job = QueueFailedJob::find($request->id);

        return [
            'message' => $job->exception,
        ];
    }
}
