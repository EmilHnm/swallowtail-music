<?php

namespace App\Orchid\Screens\Song;

use App\Enum\PermissionEnum;
use App\Http\Controllers\admin\ArtistAdminController;
use App\Models\Artist;
use App\Orchid\Layouts\Song\Artist\EditArtistDetailLayout;
use App\Orchid\Layouts\Song\Artist\GroupArtistLayout;
use App\Orchid\Layouts\Song\Artist\ImageArtistLayout;
use App\Orchid\Screens\Traits\GenerateQueryStringFilter;
use App\Orchid\Screens\Traits\HasDumpModelModal;
use App\Orchid\Screens\Traits\HasShowHideCountingToggle;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\DropDown;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Components\Cells\DateTimeSplit;
use Orchid\Screen\Layouts\Modal;
use Orchid\Screen\Screen;
use Orchid\Screen\TD;
use Orchid\Support\Facades\Layout;

class ArtistListScreen extends Screen
{
    use HasDumpModelModal, HasShowHideCountingToggle, GenerateQueryStringFilter, ArtistAdminController;
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
            ModalToggle::make("Add Artist")
                ->modal('editDetailModal')
                ->icon('plus')
                ->method('create')
                ->canSee(\Auth::user()->hasAccess(PermissionEnum::ARTIST_CREATE)),
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
                    return $artist->image_path ? "<img src='{$cover_url}' class='img-fluid rounded' width='75' height='75'>" :
                        ModalToggle::make("Upload Image")
                            ->modal('imageModal')
                            ->async('asyncPassingId')
                            ->asyncParameters(['id' => (string)$artist->artist_id])
                            ->method('uploadImage');
                }),
                TD::make('', 'Banner')->render(function(Artist $artist) {
                    $cover_url = Storage::disk('public')->url('upload/artist_image/'.$artist->banner_path);
                    return $artist->banner_path ? "<img src='{$cover_url}' class='img-fluid rounded' width='75' height='75'>" :
                        ModalToggle::make("Upload Image")
                            ->modal('imageModal')
                            ->async('asyncPassingId')
                            ->asyncParameters(['id' => (string)$artist->artist_id])
                            ->method('uploadBanner');
                }),
                TD::make('genres', "Genres")
                    ->filter()
                    ->render(function(Artist $artist) {
                        return implode("<br>", $artist->genres->map(function($genre) {
                            $query = $this->generateQueryStringFilter('id', $genre->genre_id);
                            return "<a class='orchid-custom' href='{$query}'>{$genre->name}</a>";
                        })->toArray());
                    }),
                TD::make('description', 'Description')->render(function(Artist $artist) {
                    $short_description = \Str::limit($artist->description, 20);
                    return "<span title='{$artist->description}'>{$short_description}</span>";
                }),
                TD::make('song_count', "Songs Count")->render(function(Artist $artist) {
                    $query = $this->generateQueryStringFilter('artist', $artist->artist_id);
                    $query = route('platform.app.songs') . '?' . $query;
                    return "<a class='orchid-custom' href='{$query}'>{$artist->song_count}</a>";
                })->sort(),
                TD::make('created_at', 'Created At')->sort()->filter(TD::FILTER_DATE_RANGE)->asComponent(DateTimeSplit::class),
                TD::make('updated_at', 'Updated At')->sort()->filter(TD::FILTER_DATE_RANGE)->asComponent(DateTimeSplit::class),
                TD::make('', 'Actions')->render(fn(Artist $artist) => DropDown::make()
                    ->icon('three-dots-vertical')
                        ->list([
                            ModalToggle::make("Edit Information")
                                ->modal('editDetailModal')
                                ->async('asyncPassingId')
                                ->asyncParameters(['id' => (string)$artist->artist_id])
                                ->icon('pencil')
                                ->method('updateInformation')
                                ->canSee(\Auth::user()->hasAccess(PermissionEnum::ARTIST_EDIT)),
                            ModalToggle::make("Group Artist To")
                                ->modal('groupModal')
                                ->async('asyncPassingId')
                                ->asyncParameters(['id' => (string)$artist->artist_id])
                                ->icon('person-plus-fill')
                                ->method('group')
                                ->canSee(\Auth::user()->hasAccess(PermissionEnum::ARTIST_EDIT)),
                            ModalToggle::make("Change Image")
                                ->modal('imageModal')
                                ->async('asyncPassingId')
                                ->asyncParameters(['id' => (string)$artist->artist_id])
                                ->method('uploadImage')
                                ->canSee((bool)$artist->image_path)
                                ->icon('person-square')
                                ->canSee(\Auth::user()->hasAccess(PermissionEnum::ARTIST_EDIT)),
                            ModalToggle::make("Change Banner")
                                ->modal('imageModal')
                                ->async('asyncPassingId')
                                ->asyncParameters(['id' => (string)$artist->artist_id])
                                ->method('uploadBanner')
                                ->canSee((bool)$artist->banner_path)
                                ->icon('image')
                                ->canSee(\Auth::user()->hasAccess(PermissionEnum::ARTIST_EDIT)),
                            Button::make('Delete')
                                ->method('delete')
                                ->confirm('This action is irreversible. Are you sure you want to delete this artist?')
                                ->parameters([
                                    'id' => $artist->artist_id
                                ])
                                ->icon('trash')
                                ->canSee(\Auth::user()->hasAccess(PermissionEnum::ARTIST_DELETE)),
                        ])
                ),
            ]),
            $this->getDumpModal(),
            Layout::modal('editDetailModal', [
                    EditArtistDetailLayout::class
                ])->title('Edit Artist Detail')
                ->async('asyncPassingId'),
            Layout::modal('groupModal', [
                    GroupArtistLayout::class
                ])->title('Edit Artist Detail')
                ->async('asyncPassingId'),
            Layout::modal('imageModal', [
                    ImageArtistLayout::class
                ])->title('Upload Image')
                ->async('asyncPassingId')
                ->size(Modal::SIZE_LG),

        ];
    }
}
