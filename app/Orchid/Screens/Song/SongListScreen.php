<?php

namespace App\Orchid\Screens\Song;

use App\Enum\RefererEnum;
use App\Enum\SongMetadataStatusEnum;
use App\Http\Controllers\admin\SongAdminController;
use App\Models\Song;
use App\Models\SongMetadata;
use App\Orchid\Helpers\Date;
use App\Orchid\Layouts\Song\Song\EditSongDetailLayout;
use App\Orchid\Screens\Traits\GenerateQueryStringFilter;
use App\Orchid\Screens\Traits\HasDumpModelModal;
use App\Orchid\Screens\Traits\HasShowHideCountingToggle;
use Illuminate\Database\Eloquent\Builder;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\DropDown;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Components\Cells\DateTimeSplit;
use Orchid\Screen\Fields\DateRange;
use Orchid\Screen\Screen;
use Orchid\Screen\TD;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;

class SongListScreen extends Screen
{
    use HasDumpModelModal, HasShowHideCountingToggle, GenerateQueryStringFilter, SongAdminController;
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        $songs = Song::with(['artist', 'album', 'genre', 'user'])
        ->advancedFilter([
            ['id', fn(Builder $q, $t) => $q->where('song_id', $t)->orWhere('id', $t)],
            ['title', fn(Builder $q, $t) => $q->where('title', 'like', '%' . $t . '%')],
            ['artist', fn(Builder $q, $t) => $q->whereHas('artist', fn($k) => $k->where('name', 'like', '%' . $t . '%')->orWhere('artists.artist_id', "$t"))],
            ['album', fn(Builder $q, $t) => $q->whereHas('album', fn($k) => $k->where('name', 'like', '%' . $t . '%')->orWhere('album_id', $t))],
            ['genre', fn(Builder $q, $t) => $q->whereHas('genre', fn($k) => $k->where('name', 'like', '%' . $t . '%')->orWhere('genres.genre_id', $t))],
            ['user', fn(Builder $q, $t) => $q->whereHas('user',
                fn($k) => $k->where('name', 'like', '%' . $t . '%')
                ->orWhere('email', 'like', '%' . $t . '%')
                ->orWhere('user_id', 'like', '%' . $t . '%')
            )],
            'created_at:date_range_tz',
            'updated_at:date_range_tz',
            ['referer', fn(Builder $q, $t) => $q->whereHas('file', fn($k) => $k->where('referer', $t))] ,
        ], [
            'id', 'title', 'listens', 'created_at', 'updated_at'
        ]);
        return [
            'songs' => $this->paging($songs, 15),
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Songs';
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        if($filter = \Request::get('filter')){
            if (isset($filter['referer']) && $filter['referer'] == RefererEnum::USER) {
                $filter['referer'] = null;
            }
        } else {
            $filter = array_merge($filter ?? [], ['referer' => RefererEnum::USER]);
        }
        return [
            Link::make('User Upload')
                ->icon('cloud-upload')
                ->route('platform.app.songs', ['filter' => $filter]),
            DropDown::make('Created at')
                ->icon('caret-down-fill')
                ->list([
                    Link::make('Today')->route('platform.app.songs', ['filter' => array_merge(\Request::get('filter', []), Date::filterToday()), 'dev' => 'today']),
                    Link::make('This Month')->route('platform.app.songs', ['filter' => array_merge(\Request::get('filter', []), Date::filterThisMonth()), 'dev' => 'this_month']),
                    Link::make('Last Month')->route('platform.app.songs', ['filter' => array_merge(\Request::get('filter', []), Date::filterLastMonth()), 'dev' => 'last_month']),
                ]),
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
            Layout::table('songs', [
                TD::make('id', "ID")
                    ->filter()->sort()
                    ->render(fn(Song $song) => $song->id .
                        "<br>" . $this->getDumpModelToggle(Song::class, $song->song_id, $song->song_id)),
                TD::make('title', "Title")
                    ->filter()->sort()->render(fn(Song $song) => $song->title . "<br>" .
                        "<span style='font-size: 12px'>{$song->normalized_title}</span>"
                        ),
                TD::make('artist', "Artist")
                    ->filter()
                    ->render(function(Song $song) {
                        return implode("<br>", $song->artist->map(function($artist) {
                            $query = $this->generateQueryStringFilter('id', $artist->artist_id);
//                            add artist route
                            $href =  route('platform.app.artists')."?$query";
                            $html = \Str::limit($artist->name, 20);
                            return "<a class='orchid-custom'  href=$href>" . $html . "</a>";
                        })->toArray());
                    }),
                TD::make('album', "Album")
                    ->filter()
                    ->render(function(Song $song) {
                        if ($album = $song->album) {
                            $query = $this->generateQueryStringFilter('id', $album->album_id);
    //                        add album route
                            $href =  route('platform.app.albums') . "?$query";
                            $html = \Str::limit($album->name, 20);
                            return "<a class='orchid-custom' title='{$album->name}' href=$href>" . $html . "</a>";
                        } else {
                            return '';
                        }
                    }),
                TD::make('genre', "Genre")
                    ->filter()
                    ->render(function(Song $song) {
                        return implode("<br>", $song->genre->map(function($genre) {
                            $query = $this->generateQueryStringFilter('id', $genre->genre_id);
                            $href =  route('platform.classification.genres')."?$query";
                            $html = \Str::limit($genre->name, 20);
                            return "<a class='orchid-custom'  href=$href>" . $html . "</a>";
                        })->toArray());
                    }),
                TD::make('listens', "Meta")->render(function(Song $song) {
                    $html = '';
                    $html .= "Status: " . ($song->display ?? 'Inactive') . "<br>";
                    $status_query = $this->generateQueryStringFilter('song.title', $song->song_id);
                    $status_href = route('platform.app.song-metadata') . "?$status_query";
                    $status_tag = "<a class='orchid-custom'  href=$status_href>" . SongMetadataStatusEnum::search($song->file?->status) . "</a>";
                    $html .= "File Status: " . ($status_tag ?? 'Inactive') . "<br>";
                    $html .= "Listens: " . ($song->listens ?? '0') . "<br>";
                    return $html;
                }),
                TD::make('user', "User")
                    ->filter()
                    ->render(function(Song $song) {
                        $user = $song->user;
                        $query = $this->generateQueryStringFilter('user', $user->id);
                        $href = "?$query";
//                        add user route
                        return "<a class='orchid-custom' title='{$user->name}' href=$href>" . $user->name . "</a>";
                    }),
                TD::make('updated_at', "Updated at")
                    ->sort()->filter(TD::FILTER_DATE_RANGE)
                    ->asComponent(DateTimeSplit::class),
                TD::make('created_at', "Created at")
                    ->sort()->filter(TD::FILTER_DATE_RANGE)
                    ->asComponent(DateTimeSplit::class),
                TD::make('', 'Actions')
                    ->render(fn(Song $song) => DropDown::make()
                        ->icon('three-dots-vertical')
                        ->list([
                            ModalToggle::make('Edit Information')
                                ->icon('pencil')
                                ->modal('editDetailModal')
                                ->method('update')
                                ->async('asyncPassingId')
                                ->asyncParameters(['id' => $song->song_id]),
                            Button::make('Delete')
                                ->icon('trash')
                                ->method('delete')
                                ->confirm('Are you sure you want to delete this song?')
                                ->parameters([
                                    'id' => $song->song_id,
                                ]),
                        ])
                    ),
            ]),
            $this->getDumpModal(),
            Layout::modal('editDetailModal', [
                EditSongDetailLayout::class
            ])->title('Edit Song Detail')
                ->async('asyncPassingId')

        ];
    }
}
