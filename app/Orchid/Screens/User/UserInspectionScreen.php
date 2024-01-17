<?php

namespace App\Orchid\Screens\User;

use App\Http\Controllers\admin\UserAdminController;
use App\Models\Album;
use App\Models\Playlist;
use App\Models\Song;
use App\Models\User;
use App\Orchid\Selection\MakeSelection;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Components\Cells\DateTimeSplit;
use Orchid\Screen\Fields\DateRange;
use Orchid\Screen\Fields\Group;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Screen;
use Orchid\Screen\Sight;
use Orchid\Screen\TD;
use Orchid\Support\Facades\Layout;

class UserInspectionScreen extends Screen
{
    use UserAdminController;
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(Request $request): iterable
    {
        $this->search($request);
        return [
            'user' => $this->user,
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'User Inspection';
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [];
    }

    /**
     * The screen's layout elements.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout(): iterable
    {
        return [
            MakeSelection::fields([
                Input::make('user')
                    ->placeholder('Enter id|email|link profile')
                    ->title("Search by user's information"),
            ]),
            Layout::columns([
                Layout::legend('user', [
                    Sight::make('id'),
                    Sight::make('user_id'),
                    Sight::make('name'),
                    Sight::make('email'),
                    Sight::make('gender'),
                    Sight::make('dob', 'Date of birth'),
                    Sight::make('region', 'Country'),
                    Sight::make('created_at', 'Join at')->asComponent(DateTimeSplit::class),
                    Sight::make('updated_at', 'Last update')->asComponent(DateTimeSplit::class),
                    Sight::make('deleted_at', 'Disabled at'),
                ])->title('User information'),
                Layout::blank([
                    Layout::legend('user', [
                        Sight::make('uploaded_songs_count', 'Uploaded songs')
                            ->render(fn(User $user) => '<a href="' . route('platform.app.songs', ['filter' => ['user' => $this->user->user_id]]) . '">' . $user->uploaded_songs_count . '</a>'),
                        Sight::make('uploaded_albums_count', 'Uploaded albums')
                            ->render(fn(User $user) => '<a href="' . route('platform.app.albums', ['filter' => ['user' => $this->user->user_id]]) . '">' . $user->uploaded_albums_count . '</a>'),
                        Sight::make('playlists_count', 'Total playlists created'),
                        Sight::make('likes_count', 'Total song likes'),
                        Sight::make('', 'Total Sessions')->render(function (User $user) {
                            $total = PersonalAccessToken::where('tokenable_id', $user->id)->count();
                            return $total;
                        }),
                    ])->title('User Statistic'),
                    Layout::rows([
                        Group::make([
                            Link::make('Edit user')
                                ->icon('bs.pencil')
                                ->route('platform.systems.users.edit', ['user' => $this->user ?? auth()->user()])
                                ->class('btn btn-primary')
                                ->canSee((bool)$this->user),
                            Button::make(__('Impersonate user'))
                                ->icon('bg.box-arrow-in-right')
                                ->confirm(__('You can revert to your original state by logging out.'))
                                ->method('loginAs')
                                ->class('btn btn-info')
                                ->canSee( $this->user?->id !== \request()->user()->id),
                            Button::make(__('Disable'))
                                ->icon('bs.ban')
                                ->confirm(__('Are you sure you want to disable this user?'))
                                ->method('remove', [
                                    'id' => $this->user?->id,
                                ])
                                ->class('btn btn-danger')
                                ->canSee($this->user && !$this->user->deleted_at && $this->user?->id !== \request()->user()->id),
                            Button::make(__('Enable'))
                                ->icon('bs.check2')
                                ->confirm(__('Do you really want to enable this user?'))
                                ->method('enable', [
                                    'id' => $this->user?->id,
                                ])
                                ->class('btn btn-success')
                                ->canSee($this->user && $this->user->deleted_at && $this->user?->id !== \request()->user()->id),
                        ])->autoWidth(),
                    ])->title('Actions')
                        ->canSee((bool)$this->user),
                ]),
            ])->canSee((bool)$this->user),

            Layout::table('user.uploaded_songs', [
                TD::make('id', 'ID')->render(fn(Song $song) => $song->id . '<br>' . $song->song_id)->sort(),
                TD::make('title', 'Title'),
                TD::make('created_at', 'Created at')->asComponent(DateTimeSplit::class),
                TD::make('updated_at', 'Updated at')->asComponent(DateTimeSplit::class),
            ])->title('10 recent user uploaded songs')->canSee((bool)$this->user),

            Layout::table('user.uploaded_albums', [
                TD::make('id', 'ID')->render(fn(Album $album) => $album->id . '<br>' . $album->album_id)->sort(),
                TD::make('name', 'Title'),
                TD::make('created_at', 'Created at')->asComponent(DateTimeSplit::class),
                TD::make('updated_at', 'Updated at')->asComponent(DateTimeSplit::class),
            ])->title('10 recent user uploaded albums')->canSee((bool)$this->user),

            Layout::table('user.playlists', [
                TD::make('id', 'ID')->render(fn(Playlist $playlist) => $playlist->id . '<br>' . $playlist->album_id)->sort(),
                TD::make('name', 'Title'),
                TD::make('songs_count', 'Total songs'),
                TD::make('created_at', 'Created at')->asComponent(DateTimeSplit::class),
                TD::make('updated_at', 'Updated at')->asComponent(DateTimeSplit::class),
            ])->title('10 recent user created playlists')->canSee((bool)$this->user),
        ];
    }
}
