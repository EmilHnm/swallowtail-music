<?php

namespace App\Orchid\Screens\Song;

use App\Models\Artist;
use App\Orchid\Screens\Traits\GenerateQueryStringFilter;
use App\Orchid\Screens\Traits\HasDumpModelModal;
use App\Orchid\Screens\Traits\HasShowHideCountingToggle;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Components\Cells\DateTimeSplit;
use Orchid\Screen\Fields\TextArea;
use Orchid\Screen\Layouts\Modal;
use Orchid\Screen\Screen;
use Orchid\Screen\TD;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;

class ArtistListScreen extends Screen
{
    use HasDumpModelModal, HasShowHideCountingToggle, GenerateQueryStringFilter;
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        $artists = Artist::with(['genres'])->withCount('song')->advancedFilter([
            ['id', fn(Builder $q, $t) => $q->where('artist_id', $t)->orWhere('id', $t)],
            ['name', fn(Builder $q, $t) => $q->where('name', 'like', '%' . $t . '%')],
            ['genres', fn(Builder $q, $t) => $q->whereHas('genres', fn($k) => $k->where('name', 'like', '%' . $t . '%')->orWhere('genre_id', $t))],
            'created_at:date_range_tz',
            'updated_at:date_range_tz',
        ], [
            'id',
            'name',
            'created_at',
            'updated_at',
            'song_count'
        ]);
        return [
            'artists' => $this->paging($artists, 15),
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Artists';
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            $this->getCountingToggleLink(),
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
            Layout::table('artists', [
                TD::make('id', 'ID')
                    ->render(fn(Artist $artist) => $artist->id .'<br\/>'
                        . $this->getDumpModelToggle(Artist::class, $artist->artist_id, $artist->artist_id))
                    ->sort(),
                TD::make('name', 'Name')
                    ->sort()
                    ->filter(),
                TD::make('', 'Image')->render(function(Artist $artist) {
                    $cover_url = Storage::disk('public')->url('upload/artist_image/'.$artist->image_path);
                    return $artist->image_path ? "<img src='{$cover_url}' class='img-fluid rounded' width='75' height='75'>" : 'Empty';
                }),
                TD::make('genres', "Genres")
                    ->filter()
                    ->render(function(Artist $artist) {
                        return implode("<br>", $artist->genres->map(function($genre) {
                            $query = $this->generateQueryStringFilter('genre', $genre->genre_id);
                            return "<a class='orchid-custom' href='{$query}'>{$genre->name}</a>";
                        })->toArray());
                    }),
                TD::make('description', 'Description')->render(function(Artist $artist) {
                    return ModalToggle::make('Edit')
                        ->modal('descriptionModal')
                        ->async('asyncDescriptionEdit')
                        ->class('btn text-primary')
                        ->method('saveDescription')
                        ->asyncParameters(['id' => (string)$artist->artist_id])
                        ->icon('pencil');
                }),
                TD::make('song_count', "Songs")->render(function(Artist $artist) {
                    $query = $this->generateQueryStringFilter('artist', $artist->artist_id);
                    $query = route('platform.app.songs') . '?' . $query;
                    return "<a class='orchid-custom' href='{$query}'>{$artist->song_count}</a>";
                })->sort(),
                TD::make('created_at', 'Created At')->sort()->filter(TD::FILTER_DATE_RANGE)->asComponent(DateTimeSplit::class),
                TD::make('updated_at', 'Updated At')->sort()->filter(TD::FILTER_DATE_RANGE)->asComponent(DateTimeSplit::class),
            ]),
            $this->getDumpModal(),
            Layout::modal('descriptionModal', [
                Layout::rows([
                    TextArea::make('description')
                        ->title('Description')
                        ->value(function() {
                            $request = collect(\Request::all());
                            $artist = Artist::find($request->get('id'));
                            return $artist?->description ?? $request->get('id');
                        })->rows(20),
                    ]),
                ])->title('Edit Description')
            ->async('asyncDescriptionEdit'),
        ];
    }

    public function asyncDescriptionEdit(Request $request)
    {
        return [
            'data' => $request->get('id'),
        ];
    }

    public function saveDescription(Request $request) {
        $id = $request->get('id');
        $description = $request->get('description');

        try {
            $artist = Artist::find($id);
            $artist->description = $description;
            $artist->save();
        } catch (\Exception $e) {
            Toast::error($e->getMessage());
            return redirect()->back();
        }

        Toast::info('Description saved');
        return redirect()->route('platform.app.artists');
    }
}
