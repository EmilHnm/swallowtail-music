<?php

namespace App\Orchid\Screens\Classification;

use App\Models\Genre;
use App\Orchid\Helpers\Text;
use App\Orchid\Screens\Traits\GenerateQueryStringFilter;
use App\Orchid\Screens\Traits\HasDumpModelModal;
use App\Orchid\Screens\Traits\HasShowHideCountingToggle;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Components\Cells\DateTimeSplit;
use Orchid\Screen\Fields\TextArea;
use Orchid\Screen\Screen;
use Orchid\Screen\TD;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;

class GenreListScreen extends Screen
{
    use HasDumpModelModal, HasShowHideCountingToggle, GenerateQueryStringFilter;
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        $genres = Genre::withCount('song')->withCount('artist')->withCount('album')->advancedFilter([
            ['id', fn(Builder $q, $t) => $q->where('genre_id', $t)->orWhere('id', $t)],
            ['name', fn(Builder $q, $t) => $q->where('name', 'like', '%' . $t . '%')],
            'created_at:date_range_tz',
            'updated_at:date_range_tz',
        ], [
            'id',
            'name',
            'created_at',
            'updated_at',
            'song_count',
            'artist_count',
            'album_count'
        ]);
        return [
            'genres' => $this->paging($genres, 15),
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Genres';
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
            Layout::table('genres', [
                TD::make('id', 'ID')
                    ->render(fn(Genre $genre) => $genre->id .'<br\/>'
                        . $this->getDumpModelToggle(Genre::class, $genre->genre_id, $genre->genre_id))
                ->sort()->filter(),
                TD::make('name', 'Name')->filter()->sort(),
                TD::make('description', 'Description')
                    ->render(function(Genre $genre) {
                        return Text::limit($genre->description, 50)
                            . ModalToggle::make('Edit')
                                ->modal('descriptionModal')
                                ->async('asyncDescriptionEdit')
                                ->class('btn text-primary')
                                ->method('saveDescription')
                                ->asyncParameters(['id' => (string)$genre->genre_id])
                                ->icon('pencil');
                    }),
                TD::make('', 'Meta')->render(function(Genre $genre) {
                    $html = '';
                    $query = $this->generateQueryStringFilter('genre', $genre->genre_id);
                    $url = route('platform.app.songs'). "?$query";
                    $html .= "Songs: " . "<a class='orchid-custom' title='{$genre->name}' href=$url>" . $genre->song_count . "</a>" . "<br>";
                    $url = route('platform.app.albums'). "?$query";
                    $html .= "Albums: " . "<a class='orchid-custom' title='{$genre->name}' href=$url>" . $genre->album_count . "</a>" . "<br>";
                    $url = route('platform.app.artists'). "?$query";
                    $html .= "Artists: " . "<a class='orchid-custom' title='{$genre->name}' href=$url>" . $genre->artist_count . "</a>" . "<br>";
                    return $html;
                }),
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
                            $artist = Genre::find($request->get('id'));
                            return $artist?->description ?? $request->get('id');
                        })->rows(20),
                ]),
            ])->title('Edit Description')
                ->async('asyncDescriptionEdit')
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
            $artist = Genre::find($id);
            $artist->description = $description;
            $artist->save();
        } catch (\Exception $e) {
            Toast::error($e->getMessage());
            return redirect()->back();
        }

        Toast::info('Description saved');
        return redirect()->route('platform.classification.genres');
    }
}
