<?php

namespace App\Orchid\Screens\Platform;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\DropDown;
use Orchid\Screen\Screen;
use Orchid\Screen\TD;
use Orchid\Support\Facades\Layout;

class BackupListScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        $backups = collect(\Storage::disk('local')->allFiles('backups'));
        $backups = $backups->map(function ($item) {
            return [
                'name' => $item,
                'size' => \Storage::disk('local')->size($item),
                'date' => \Storage::disk('local')->lastModified($item),
            ];
        });
        $backups = new LengthAwarePaginator($backups, $backups->count(), 10, Paginator::resolveCurrentPage(), ['path' => Paginator::resolveCurrentPath()]);
        $backups->setCollection($backups->getCollection()->sortByDesc('date'));
        return [
            'backups' => $backups,
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Backup Database';
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            Button::make('Create Backup')
                ->icon('cloud-upload')
                ->method('create')
                ->confirm('Are you sure you want to create a backup?'),
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
            Layout::table('backups', [

                TD::make('name', 'Name')->render(function ($item) {
                    return $item['name'];
                }),
                TD::make('size', 'Size')->render(function ($item) {
                    return number_format($item['size'] / 1024, 2) . ' KB';
                }),
                TD::make('date', 'Date')->render(function ($item) {
                    return date('Y-m-d H:i:s', $item['date']);
                }),
                TD::make('actions', 'Actions')->render(function ($item) {
                    return DropDown::make()->icon('three-dots-vertical')
                        ->list([
                            Button::make('Download')
                                ->method('download')
                                ->icon('cloud-download')
                                ->parameters([
                                    'name' => $item['name'],
                                ])
                                ->turbo(false),
                            Button::make('Delete')
                                ->method('delete')
                                ->icon('trash')
                                ->parameters([
                                    'name' => $item['name'],
                                ])
                                ->confirm('Are you sure you want to delete this backup?')
                        ]);
                }),
            ]),
        ];
    }

    public function download($name)
    {
        return response()->download(storage_path('app/' . $name));
    }
    public function delete($name)
    {
        \Storage::disk('local')->delete($name);
        return redirect()->route('platform.backups');
    }
    public function create()
    {
        \Artisan::call('db:backup', [
            '-D' => true,
        ]);
        return redirect()->route('platform.backups');
    }
}
