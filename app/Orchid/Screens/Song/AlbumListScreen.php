<?php

namespace App\Orchid\Screens\Song;

use App\Enum\AlbumTypeEnum;
use App\Enum\RefererEnum;
use App\Models\Album;
use App\Orchid\Screens\Traits\GenerateQueryStringFilter;
use App\Orchid\Screens\Traits\HasDumpModelModal;
use App\Orchid\Screens\Traits\HasShowHideCountingToggle;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;
use Orchid\Screen\Components\Cells\DateTimeSplit;
use Orchid\Screen\Screen;
use Orchid\Screen\TD;
use Orchid\Support\Facades\Layout;

class AlbumListScreen extends Screen
{
    use HasDumpModelModal, HasShowHideCountingToggle, GenerateQueryStringFilter;
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        $albums = Album::withCount(['song'])->with(['genre'])->advancedFilter([
            ['id', fn($q, $t) => $q->where('album_id', $t)->orWhere('id', $t)],
            ['name', fn(Builder $q, $t) => $q->where('name', 'like', '%' . $t . '%')],
            ['type', fn($q, $t) => $q->where('type', $t)],
            ['genre', fn(Builder $q, $t) => $q->whereHas('genre', fn($k) => $k->where('name', 'like', '%' . $t . '%'))],
            'created_at:date_range_tz',
            'updated_at:date_range_tz',
        ], [
            'id', 'name', 'type', 'created_at', 'updated_at'
        ]);
        return [
            "albums" => $this->paging($albums, 15),
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Albums';
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
            Layout::table('albums', [
                TD::make('id', 'ID')
                    ->render(fn(Album $album) => $album->id .'<br\/>'
                     . $this->getDumpModelToggle(Album::class, $album->album_id, $album->album_id, ['with'=> ['song', 'genre']]))
                    ->sort(),
                TD::make('name', 'Name')->render(function(Album $album) {
                    $name = \Str::limit($album->name, 30);
                    return "<span class='' title='{$name}'>" . $name . "</span>";
                })->sort(),
                TD::make('type', 'Type')->sort()->filter(TD::FILTER_SELECT)->filterOptions(
                    AlbumTypeEnum::toArray()
                ),
                TD::make('', 'Cover')->render(function(Album $album) {
                    $cover_url = Storage::disk('public')->url('upload/album_cover'.$album->image_path);
                    return "<img src='{$cover_url}' class='img-fluid rounded' width='75' height='75'>";
                }),
                TD::make('genre', "Genre")
                    ->filter()
                    ->render(function(Album $album) {
                        return implode("<br>", $album->genre->map(function($genre) {
                            $query = $this->generateQueryStringFilter('genre', $genre->genre_id);
//                            add genre route
                            $href =  "?$query";
                            $html = \Str::limit($genre->name, 20);
                            return "<a class='orchid-custom'  href=$href>" . $html . "</a>";
                        })->toArray());
                    }),
                TD::make('', 'Meta')->render(function(Album $album){
                    $html = '';
                    $query = $this->generateQueryStringFilter('album', $album->album_id);
//                            add genre route
                    $href =  route('platform.app.songs') . "?$query";
                    $html .= "<a class='orchid-custom'  href=$href>Songs count: " . $album->song_count . "</a>";
                    $html .= "<br>";
                    $html .= "<span class=''>Released: {$album->release_year} </span>";
                    $html .= "<br>";
                    $referer = RefererEnum::search($album->referer);
                    $html .= "<span class=''>Referer: {$referer} </span>";
                    return $html;
                }),
                TD::make('created_at', 'Created At')->sort()->filter(TD::FILTER_DATE_RANGE)->asComponent(DateTimeSplit::class),
                TD::make('updated_at', 'Updated At')->sort()->filter(TD::FILTER_DATE_RANGE)->asComponent(DateTimeSplit::class),
            ]),
            $this->getDumpModal(),
        ];
    }
}
