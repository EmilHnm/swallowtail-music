<?php

namespace App\Orchid\Screens\Queue;

use App\Enum\MonitorStatus;
use App\Models\Queue\Monitor;
use App\Orchid\Screens\Traits\GenerateQueryStringFilter;
use App\Orchid\Screens\Traits\HasDumpModelModal;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\DropDown;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Screen;
use Orchid\Screen\TD;
use Orchid\Support\Facades\Layout;
use romanzipp\QueueMonitor\Controllers\ShowQueueMonitorController;

class MonitorJobsListScreen extends Screen
{
    use GenerateQueryStringFilter, HasDumpModelModal;

    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        $metrics = app(ShowQueueMonitorController::class)->collectMetrics()->metrics;
        $query = Monitor::query()
            ->advancedFilter(
                [
                    'status',
                    'job_id',
                    'queue',
                    'name',
                ],
                [
                    'id',
                    'started_at',
                    'duration',

                ])
            ->defaultSortBy('id', 'desc')
            ->simplePaginate();

        return [
            'jobs' => $query,
            'metrics' => $metrics,
        ];
    }

    /**
     * The name of the screen displayed in the header.
     */
    public function name(): ?string
    {
        return 'MonitorJobsListScreen';
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            Link::make('Jobs')->route('platform.queue.jobs'),
            Link::make('Fail jobs')->route('platform.queue.failed_jobs'),
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
            Layout::modal('showException',
                [
                    Layout::view('vendor.platform.dump_exception'),
                ])
                ->async('asyncGetData')
                ->size('modal-xl')
                ->withoutApplyButton()
                ->title('Exception'),
            Layout::view('vendor.platform.metrics_custom'),

            $this->getDumpModal(),
            Layout::table('jobs', [
                    TD::make('id')
                        ->sort()
                        ->render(fn (Monitor $m) => $m->id.self::getDumpModelToggle(Monitor::class, $m->id)),
                    TD::make('name', 'Job')->filter()
                        ->render(function (Monitor $m) {
                            return $m->getBasename();
                        }),
                    TD::make('status')
                        ->filter(
                            Select::make('Status')
                                ->options(array_flip(\App\Enum\MonitorStatus::toArray()))->empty('All'))
                        ->filterValue(fn ($v) => \App\Enum\MonitorStatus::search((int) $v))
                        ->render(fn (Monitor $m) => \App\Enum\MonitorStatus::renderStatus($m->status)),
                    TD::make('queue', 'Details')
                        ->filter()
                        ->render(function (Monitor $m) {
                            return 'Queue : '.$m->queue.'<br>'.'Attempt: '.$m->attempt;
                        }),
                    TD::make('data', 'Custom Data')
                        ->render(fn (Monitor $m) => ! $m->getData() ? '-' : ModalToggle::make('Data')
                            ->modal('showData')
                            ->async('asyncGetData')
                            ->asyncParameters([
                                'id' => (string) $m->id,
                                'model' => Monitor::class,
                                'title' => 'Custom data',
                                'method' => 'getData',
                            ])),
                    TD::make('progress', 'Progress')
                        ->render(fn (Monitor $m) => $m->progress ?? '-'),
                    TD::make('duration', 'Duration')
                        ->render(fn (Monitor $m) => $m->getElapsedInterval()->format('%H:%I:%S')),
                    TD::make('retried')->render(fn (Monitor $m) => $m->retried ? 'true' : 'false'),
                    TD::make('started_at', 'Started at')
                        ->sort()
                        ->render(fn (Monitor $m) => $m->started_at?->diffForHumans() ?? '-'),
                    TD::make('exception', 'Error')
                        ->render(function (Monitor $m) {
                            if (! $m->exception) {
                                return '-';
                            }

                            return ModalToggle::make('Error')
                                ->modal('showException')
                                ->async('asyncGetData')
                                ->asyncParameters([
                                    'id' => (string) $m->id,
                                ]);
                            //                        return \Str::limit($m->exception_message, 30);

                        }),
                    TD::make('', 'Actions')
                        ->render(function (Monitor $m) {
                            $action = [];
                            if ($m->canBeRetried()) {
                                $action[] = Button::make('Retry')->method('retry', ['id' => $m->id]);
                            }
                            $action[] = Button::make('Delete')
                                ->confirm('Are you sure to delete this job?')
                                ->method('delete', ['id' => $m->id]);

                            return DropDown::make('')
                                ->icon('bs.three-dots-vertical')
                                ->list($action);
                        }),

                ]),
        ];
    }

    public function delete(Request $request)
    {
        $id = $request->get('id');
        Monitor::query()->find($id)->delete();
        \Orchid\Support\Facades\Toast::success('Deleted successfully!');
    }

    public function retry(Request $request)
    {
        $id = $request->get('id');
        $monitor = Monitor::query()
            ->where('status', MonitorStatus::FAILED)
            ->where('retried', false)
            ->whereNotNull('job_uuid')
            ->findOrFail($id);
        if ($monitor) {
            $monitor->retry();
        }

    }

    public function asyncGetData(Request $request)
    {
        $monitor = Monitor::find($request->id);

        return [
            'message' => $monitor->exception_message,
            'trace' => $monitor->getException()?->getTraceAsString(),
        ];
    }
}
