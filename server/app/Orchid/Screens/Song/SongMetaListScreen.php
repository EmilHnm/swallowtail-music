<?php

namespace App\Orchid\Screens\Song;

use App\Enum\SongMetadataStatusEnum;
use App\Enum\RefererEnum;
use App\Http\Controllers\admin\SongMetadataAdminController;
use App\Models\Song;
use App\Models\SongMetadata;
use App\Orchid\Helpers\FileSize;
use App\Orchid\Layouts\Song\Song\SongLyricEditLayout;
use App\Orchid\Layouts\Song\Song\SongMetadataStatusEditLayout;
use App\Orchid\Screens\Traits\GenerateQueryStringFilter;
use App\Orchid\Screens\Traits\HasDumpModelModal;
use App\Orchid\Screens\Traits\HasShowHideCountingToggle;
use App\Services\SongManager;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Number;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\DropDown;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Components\Cells\DateTimeSplit;
use Orchid\Screen\Fields\DateRange;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Fields\TextArea;
use Orchid\Screen\Screen;
use Orchid\Screen\TD;
use Orchid\Support\Facades\Alert;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;

class SongMetaListScreen extends Screen
{
    use HasDumpModelModal, HasShowHideCountingToggle, GenerateQueryStringFilter, SongMetadataAdminController;

    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        $metadata = SongMetadata::with(['song' => fn($q) => $q->withoutGlobalScopes(['playable'])])->advancedFilter(
            [
                ['id', fn (Builder $q, $t) => $q->where('id', $t)->orWhere('hash', 'like',  $t . '%')],
                'created_at:date_range_tz',
                'updated_at:date_range_tz',
                ['song.title', fn (Builder $q, $t) =>
                $q->whereHas(
                    'song',
                    fn (Builder $q) =>
                    $q->where('title', 'like', "%$t%")
                        ->orWhere('song_id', $t)
                )],
                'referer',
                ['driver', fn (Builder $q, $t) => $q->where('driver', $t)],
            ],
            [
                'id', 'song.title', 'status', 'driver', 'size', 'referer', 'created_at', 'updated_at'
            ]
        );

        return [
            'metadata' => $this->paging($metadata, 15),
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Songs Metadata';
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
            Layout::table('metadata', [
                TD::make('id', 'ID')->render(fn (SongMetadata $metadata) =>
                $metadata->id . '<br>' .
                    $this->getDumpModelToggle(
                        SongMetadata::class,
                        $metadata->id,
                        \Str::limit($metadata->hash, 10)
                    ))->sort()->filter(),
                TD::make('song.title', 'Title')->render(function (SongMetadata $songMetadata) {
                    $query = $this->generateQueryStringFilter('id', $songMetadata->song_id);
                    //                            add artist route
                    $href =  route('platform.app.songs') . "?$query";
                    $html = \Str::limit($songMetadata->song->title, 20);
                    return "<a class='orchid-custom'  href=$href>" . $html . "</a>";
                })->filter(),
                TD::make('status', 'Status')->render(
                    fn (SongMetadata $metadata) => SongMetadataStatusEnum::search($metadata->status)
                )
                    ->filter(Select::make()->options(array_flip(SongMetadataStatusEnum::toArray()))->empty('All'))->sort(),
                TD::make('', 'Lyric')->render(function (SongMetadata $metadata) {
                    return ModalToggle::make('Lyric')
                        ->modal('lyricModal')
                        ->async('asyncPassingId')
                        ->class('btn text-primary')
                        ->method('saveLyrics')
                        ->asyncParameters(['id' => (string)$metadata->id])
                        ->icon('pencil');
                }),
                TD::make('driver', 'Storage Driver')->sort()->filter(TD::FILTER_SELECT)->filterOptions(
                    array_combine(array_keys(config('filesystems.disks')),array_keys(config('filesystems.disks')))
                )->sort(),
                TD::make('size', 'Size')->render(fn (SongMetadata $metadata) => $metadata->size ? Number::fileSize($metadata->size) : 'N/A')->sort(),
                TD::make('referer', 'Referer')->render((fn (SongMetadata $metadata) => RefererEnum::search($metadata->referer)))
                    ->filter(Select::make()->options(array_flip(RefererEnum::toArray()))->empty('All')),
                TD::make('created_at', 'Created At')->asComponent(DateTimeSplit::class)->sort()->filter(TD::FILTER_DATE_RANGE),
                TD::make('updated_at', 'Updated At')->asComponent(DateTimeSplit::class)->sort()->filter(TD::FILTER_DATE_RANGE),
                TD::make()->render(
                    fn (SongMetadata $metadata) => DropDown::make()->icon('three-dots-vertical')->list([
                        Button::make('Publish')
                            ->icon('upload')
                            ->confirm('Are you sure you want to publish this metadata?')
                            ->method('publish')
                            ->parameters(['id' => $metadata->id])->canSee(!in_array($metadata->status,array_merge(SongMetadataStatusEnum::notReadyStatus(), [SongMetadataStatusEnum::PUBLISH]))),
                        ModalToggle::make('Set Errors')
                            ->icon('flag')
                            ->method('asyncPassingId')
                            ->parameters(['id' => $metadata->id])
                            ->modal('statusModal')
                            ->method('setErrors')
                            ->canSee($metadata->status == SongMetadataStatusEnum::DONE or $metadata->status == SongMetadataStatusEnum::PUBLISH),
                        Button::make('Download')
                            ->icon('download')
                            ->method('download')
                            ->parameters(['id' => $metadata->id])
                            ->turbo(false),
                    ]),
                ),
            ]),
            $this->getDumpModal(),
            Layout::modal('statusModal',SongMetadataStatusEditLayout::class)
                ->title('Set Errors')
                ->applyButton('Save')
                ->async('asyncPassingId')
                ->method('setErrors'),
            Layout::modal('lyricModal', [
                SongLyricEditLayout::class
            ])->title('Lyric Edit')
                ->applyButton('Save')
                ->async('asyncPassingId')
                ->method('saveLyrics'),
        ];
    }


}
