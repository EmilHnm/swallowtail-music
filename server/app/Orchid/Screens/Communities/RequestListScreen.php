<?php

namespace App\Orchid\Screens\Communities;

use App\Enum\RequestStatusEnum;
use App\Http\Controllers\admin\RequestAdminController;
use App\Models\Request;
use App\Orchid\Layouts\Request\ResponseRequestLayout;
use App\Orchid\Layouts\RequestDetailsModal;
use App\Orchid\Screens\Traits\GenerateQueryStringFilter;
use App\Orchid\Screens\Traits\HasDumpModelModal;
use App\Orchid\Screens\Traits\HasShowHideCountingToggle;
use Illuminate\Database\Eloquent\Builder;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\DropDown;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Components\Cells\DateTimeSplit;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Layouts\Modal;
use Orchid\Screen\Screen;
use Orchid\Screen\TD;
use Orchid\Support\Color;
use Orchid\Support\Facades\Layout;

class RequestListScreen extends Screen
{
    use HasDumpModelModal, HasShowHideCountingToggle, GenerateQueryStringFilter, RequestAdminController;
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        $requests = Request::advancedFilter([
            'id:int',
            ['type',  fn(Builder $q, $t) => $q->where('type', $t)],
            ['status',  fn(Builder $q, $t) => $q->where('status', $t)],
            ['body.name', fn(Builder $q, $t) => $q->where('body->name', 'like', '%' . $t . '%')],
            ['user_fillable',  fn(Builder $q, $t) => $q->where('user_fillable', $t)],
            'created_at:date_range',
            'updated_at:date_range',
        ], [
            'id',
            'type',
            'status',
            'user_fillable',
            'created_at',
            'updated_at',
        ])->withCount('responses')->with('filledBy');
        return [
            'requests' => $this->paging($requests, 15),
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Requests';
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
            Layout::table('requests', [
                TD::make('id', 'Id')->sort()->filter(TD::FILTER_NUMBER_RANGE)
                    ->render(fn(Request $request) => $this->getDumpModelToggle(Request::class ,$request->id, $request->id)),
                TD::make('type', 'Type')->filter(TD::FILTER_SELECT)->filterOptions([
                    '' => 'All',
                    'album' => 'Album',
                    'artist' => 'Artist',
                    'song' => 'Song',
                    'genre' => 'Genre',
                ]),
                TD::make('body.name', "Title")->filter(),
                TD::make('status', 'Status')
                    ->render(fn(Request $request) => RequestStatusEnum::search($request->status))
                    ->filter(Select::make()->options(array_flip(RequestStatusEnum::toArray()))->empty('All')),
//                todo: add responses count
                TD::make('responses_count', 'Responses'),
                TD::make('user_fillable', 'User Fillable')->render(fn(Request $request) => $request->user_fillable ? "Yes" : "No")->filter(
                    Select::make()->options([
                        '' => 'All',
                        '1' => 'Yes',
                        '0' => 'No',
                    ])
                ),
                TD::make('created_at', 'Created At')->sort()->filter(TD::FILTER_DATE_RANGE)->asComponent(DateTimeSplit::class),
                TD::make('updated_at', 'Updated At')->sort()->filter(TD::FILTER_DATE_RANGE)->asComponent(DateTimeSplit::class),
                TD::make('', 'Actions')->render(function(Request $request) {
                    return DropDown::make()->icon('three-dots-vertical')->list([
                        ModalToggle::make('Details')->icon('eye')->modal('requestDetailsModal')->asyncParameters([
                            'id' => $request->id,
                        ])->async('asyncPassingModal'),
                        ModalToggle::make('Response')->icon('pencil')->modal('responseModal')->method('response')->asyncParameters([
                            'id' => $request->id,
                        ])->async('asyncPassingId')->canSee($request->status === RequestStatusEnum::PENDING),
                        Button::make('Deny')->icon('ban')->type(Color::DANGER)->confirm('Are you sure you want to deny this request?')->method('deny')->parameters([
                            'id' => $request->id,
                        ])->canSee($request->status === RequestStatusEnum::PENDING),
                    ]);
                }),
            ]),
            $this->getDumpModal(),
            Layout::modal('responseModal', [
                ResponseRequestLayout::class
            ])->applyButton('Response')->title('Response')->async('asyncPassingId'),
            (new RequestDetailsModal('requestDetailsModal',  [], 'id'))
                ->withoutApplyButton()
                ->async('asyncPassingModal')
                ->title('Request Details')
                ->size(Modal::SIZE_LG),
        ];
    }
}
