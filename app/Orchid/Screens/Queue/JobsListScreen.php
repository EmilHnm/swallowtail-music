<?php

namespace App\Orchid\Screens\Queue;

use App\Models\Queue\Job;
use App\Orchid\Helpers\Date;
use App\Orchid\Screens\Traits\HasDumpModelModal;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Screen;
use Orchid\Screen\TD;
use Orchid\Support\Facades\Layout;

class JobsListScreen extends Screen
{
    use HasDumpModelModal;

    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        $query = Job::query()
            ->advancedFilter(
                [
                    'id',
                ],
                [
                    'id',
                    'available_at',
                    'created_at',
                ]);

        return [
            'jobs' => $query->paginate(),
        ];
    }

    /**
     * The name of the screen displayed in the header.
     */
    public function name(): ?string
    {
        return 'JobsListScreen';
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
            $this->getDumpModal(),
            Layout::table('jobs', [
                TD::make('id')
                    ->render(fn (Job $job) => $job->id.self::getDumpModelToggle(Job::class, $job->id))
                    ->filter()
                    ->sort(),
                TD::make('queue'),
                TD::make('payload')->render(fn (Job $job) => ModalToggle::make('Payload')
                    ->modal('showData')
                    ->asyncParameters([
                        'id' => (string) $job->id,
                        'model' => Job::class,
                        'title' => 'Payload',
                        'property' => 'payload',
                    ])),
                TD::make('attempts'),
                TD::make('reserved_at'),
                TD::make('available_at')
                    ->sort()
                    ->render(Date::human('available_at')),
                TD::make('created_at')
                    ->sort()
                    ->render(Date::human('created_at')),
            ]),
        ];
    }

    public function asyncGetData()
    {
        return [

        ];
    }
}
