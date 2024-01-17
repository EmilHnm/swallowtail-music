<?php

namespace App\Http\Controllers\admin;

use App\Models\Song;
use App\Models\Album;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Orchid\Access\Impersonation;
use Orchid\Support\Facades\Toast;

trait UserAdminController
{
    /**
     * @return array
     */
    public function asyncGetUser(User $user): iterable
    {
        return [
            'user' => $user,
        ];
    }

    public function saveUser(Request $request, User $user): void
    {
        $request->validate([
            'user.email' => [
                'required',
                Rule::unique(User::class, 'email')->ignore($user),
            ],
        ]);

        $user->fill($request->input('user'))->save();

        Toast::info(__('User was saved.'));
    }

    public function remove(Request $request): void
    {
        User::findOrFail($request->get('id'))->delete();

        Toast::info(__('User was disabled.'));
    }

    public function enable(Request $request): void
    {
        User::withTrashed()->findOrFail($request->get('id'))->restore();

        Toast::info(__('User was enabled.'));
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function save(User $user, Request $request)
    {
        $request->validate([
            'user.email' => [
                'required',
                Rule::unique(User::class, 'email')->ignore($user),
            ],
        ]);

        $permissions = collect($request->get('permissions'))
            ->map(fn ($value, $key) => [base64_decode($key) => $value])
            ->collapse()
            ->toArray();

        $user->when($request->filled('user.password'), function (Builder $builder) use ($request) {
            $builder->getModel()->password = Hash::make($request->input('user.password'));
        });

        $user
            ->fill($request->collect('user')->except(['password', 'permissions', 'roles'])->toArray())
            ->fill(['permissions' => $permissions])
            ->save();

        $user->replaceRoles($request->input('user.roles'));

        Toast::info(__('User was saved.'));

        return redirect()->route('platform.systems.users');
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function loginAs(User $user)
    {
        Impersonation::loginAs($user);

        Toast::info(__('You are now impersonating this user'));

        return redirect()->route(config('platform.index'));
    }
    //

    public function search(Request $request) {
        $date = $request->input('filter.date', []);
        $this->start_date = $date['start'] ?? null;
        $this->end_date = $date['end'] ?? $this->start_date;
        if($search = $request->input('filter.user', '')){
            if (\Str::isMatch('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', $search)) {
                $this->user = User::withTrashed()->with(
                    [
                        'uploaded_songs' => function ($q) {
                            $q->where('display', 'public')->limit(10)->orderBy('created_at', 'desc');
                        },
                        'playlists' => function ($q) {
                            $q->limit(10)->orderBy('created_at', 'desc')->withCount('song');
                        },
                        'uploaded_albums'=> function ($q) {
                            $q->limit(10)->orderBy('created_at', 'desc')->withCount('song');
                        },
                    ]
                )->withCount('likes')->where('email', $search)->first();
            } elseif (\Str::isUrl($search)) {
                preg_match('/\/user\/([a-f\d]{8}-[a-f\d]{4}-[a-f\d]{4}-[a-f\d]{4}-[a-f\d]{12})/', $search, $matches);
                $this->user = User::withTrashed()->with(
                    [
                        'uploaded_songs' => function ($q) {
                            $q->where('display', 'public')->limit(10)->orderBy('created_at', 'desc');
                        },
                        'playlists' => function ($q) {
                            $q->limit(10)->orderBy('created_at', 'desc')->withCount('song');
                        },
                        'uploaded_albums' => function ($q) {
                            $q->limit(10)->orderBy('created_at', 'desc')->withCount('song');
                        },
                    ]
                )->withCount('likes')
                    ->withCount('uploaded_songs')
                    ->withCount('playlists')
                    ->withCount('uploaded_albums')->find($matches[1]);
            } elseif (\Str::isUuid($search)) {
                $this->user = User::withTrashed()->with(
                    [
                        'uploaded_songs' => function ($q) {
                            $q->where('display', 'public')->limit(10)->orderBy('created_at', 'desc');
                        },
                        'playlists' => function ($q) {
                            $q->limit(10)->orderBy('created_at', 'desc')->withCount('song');
                        },
                        'uploaded_albums' => function ($q) {
                            $q->limit(10)->orderBy('created_at', 'desc')->withCount('song');
                        },
                    ]
                )->withCount('likes')
                    ->withCount('uploaded_songs')
                    ->withCount('playlists')
                    ->withCount('uploaded_albums')->where('user_id', $search)->first();
            } elseif (is_numeric($search)) {
                $this->user = User::withTrashed()->with(
                    [
                        'uploaded_songs' => function ($q) {
                            $q->where('display', 'public')->limit(10)->orderBy('created_at', 'desc');
                        },
                        'playlists' => function ($q) {
                            $q->limit(10)->orderBy('created_at', 'desc')->withCount('song');
                        },
                        'uploaded_albums' => function ($q) {
                            $q->limit(10)->orderBy('created_at', 'desc')->withCount('song');
                        },
                    ]
                )
                    ->withCount('likes')
                    ->withCount('uploaded_songs')
                    ->withCount('playlists')
                    ->withCount('uploaded_albums')
                    ->find($search);
            } else {
                $this->user = null;
            }
        } else {
            $this->user = null;
        }
    }

    public function showUserUploadSong($id)
    {
        $songs = Song::whereHas("user", function ($q) use ($id) {
            $q->where("user_id", $id);
        })
            ->where("display", "public")
            ->with(["album","artist"])
            ->get();

        return response()->json([
            "status" => "success",
            "songs" => $songs,
        ]);
    }

    public function showUserUploadAlbum($id)
    {
        $albumUploaded = Album::whereHas("user", function ($q) use ($id) {
            $q->where("user_id", $id);
        })
            ->withCount("song")
            ->get();
        return response()->json([
            "status" => "success",
            "album" => $albumUploaded,
        ]);
    }
}
