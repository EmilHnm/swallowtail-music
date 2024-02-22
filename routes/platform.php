<?php

declare(strict_types=1);

use App\Enum\PermissionEnum;
use App\Orchid\Screens\Examples\ExampleScreen;
use App\Orchid\Screens\PlatformScreen;
use App\Orchid\Screens\Role\RoleEditScreen;
use App\Orchid\Screens\Role\RoleListScreen;
use App\Orchid\Screens\Tools\UserInspectionScreen;
use App\Orchid\Screens\User\UserEditScreen;
use App\Orchid\Screens\User\UserListScreen;
use App\Orchid\Screens\User\UserProfileScreen;
use Illuminate\Support\Facades\Route;
use Tabuna\Breadcrumbs\Trail;

/*
|--------------------------------------------------------------------------
| Dashboard Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the need "dashboard" middleware group. Now create something great!
|
*/

// Main
Route::screen('/main', PlatformScreen::class)
    ->name('platform.main');

// Platform > Profile
Route::screen('profile', UserProfileScreen::class)
    ->name('platform.profile')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.index')
        ->push(__('Profile'), route('platform.profile')));

// Platform > System > Users > User
Route::screen('users/{user}/edit', UserEditScreen::class)
    ->name('platform.systems.users.edit')
    ->breadcrumbs(fn (Trail $trail, $user) => $trail
        ->parent('platform.systems.users')
        ->push($user->name, route('platform.systems.users.edit', $user)));

// Platform > System > Users > Create
Route::screen('users/create', UserEditScreen::class)
    ->name('platform.systems.users.create')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.systems.users')
        ->push(__('Create'), route('platform.systems.users.create')));


// Platform > System > Users
Route::screen('users', UserListScreen::class)
    ->name('platform.systems.users')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.index')
        ->push(__('Users'), route('platform.systems.users')));

// Platform > System > Roles > Role
Route::screen('roles/{role}/edit', RoleEditScreen::class)
    ->name('platform.systems.roles.edit')
    ->breadcrumbs(fn (Trail $trail, $role) => $trail
        ->parent('platform.systems.roles')
        ->push($role->name, route('platform.systems.roles.edit', $role)));

// Platform > System > Roles > Create
Route::screen('roles/create', RoleEditScreen::class)
    ->name('platform.systems.roles.create')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.systems.roles')
        ->push(__('Create'), route('platform.systems.roles.create')));

// Platform > System > Roles
Route::screen('roles', RoleListScreen::class)
    ->name('platform.systems.roles')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.index')
        ->push(__('Roles'), route('platform.systems.roles')));

// Example...
Route::screen('example', ExampleScreen::class)
    ->name('platform.example')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.index')
        ->push('Example Screen'));

//Route::screen('/examples/form/fields', ExampleFieldsScreen::class)->name('platform.example.fields');
//Route::screen('/examples/form/advanced', ExampleFieldsAdvancedScreen::class)->name('platform.example.advanced');
//Route::screen('/examples/form/editors', ExampleTextEditorsScreen::class)->name('platform.example.editors');
//Route::screen('/examples/form/actions', ExampleActionsScreen::class)->name('platform.example.actions');
//
//Route::screen('/examples/layouts', ExampleLayoutsScreen::class)->name('platform.example.layouts');
//Route::screen('/examples/grid', ExampleGridScreen::class)->name('platform.example.grid');
//Route::screen('/examples/charts', ExampleChartsScreen::class)->name('platform.example.charts');
//Route::screen('/examples/cards', ExampleCardsScreen::class)->name('platform.example.cards');

//Route::screen('idea', Idea::class, 'platform.screens.idea');

Route::screen('backups', \App\Orchid\Screens\Platform\BackupListScreen::class)
    ->name('platform.backups')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.index')
        ->push(__('Backups'), route('platform.backups')));

Route::screen('songs', \App\Orchid\Screens\Song\SongListScreen::class)
    ->name('platform.app.songs')
    ->middleware(PermissionEnum::getMiddleware([PermissionEnum::SONG_INDEX]))
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.index')
        ->push(__('Songs'), route('platform.app.songs')));

Route::screen('song-metadata', \App\Orchid\Screens\Song\SongMetaListScreen::class)
    ->name('platform.app.song-metadata')
    ->middleware(PermissionEnum::getMiddleware([PermissionEnum::SONG_INDEX]))
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.index')
        ->push(__('Songs'), route('platform.app.song-metadata')));

Route::screen('albums', \App\Orchid\Screens\Song\AlbumListScreen::class)
    ->name('platform.app.albums')
    ->middleware(PermissionEnum::getMiddleware([PermissionEnum::ALBUM_INDEX]))
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.index')
        ->push(__('Albums'), route('platform.app.albums')));

Route::screen('artists', \App\Orchid\Screens\Song\ArtistListScreen::class)
    ->name('platform.app.artists')
    ->middleware(PermissionEnum::getMiddleware([PermissionEnum::ARTIST_INDEX]))
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.index')
        ->push(__('Artists'), route('platform.app.artists')));

Route::screen('genres', \App\Orchid\Screens\Classification\GenreListScreen::class)
    ->name('platform.classification.genres')
    ->middleware(PermissionEnum::getMiddleware([PermissionEnum::CLASSIFYING_INDEX]))
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.index')
        ->push(__('Genres'), route('platform.classification.genres')));

Route::screen('requests', \App\Orchid\Screens\Communities\RequestListScreen::class)
    ->name('platform.communities.requests')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.index')
        ->push(__('Requests'), route('platform.communities.requests')));

Route::screen('tools/query-inspector', \App\Orchid\Screens\Tools\ElasticSearchInspectorScreen::class)
    ->name('platform.systems.tools.query-inspector')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.index')
        ->push(__('Inspector'), route('platform.systems.tools.query-inspector')));

Route::screen('tools/user-inspector', UserInspectionScreen::class)
    ->name('platform.systems.tools.inspector')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.index')
        ->push(__('Inspector'), route('platform.systems.tools.inspector')));
