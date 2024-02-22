<?php

declare(strict_types=1);

namespace App\Orchid;

use App\Enum\PermissionEnum;
use Orchid\Platform\Dashboard;
use Orchid\Platform\ItemPermission;
use Orchid\Platform\OrchidServiceProvider;
use Orchid\Screen\Actions\Menu;
use Orchid\Support\Color;

class PlatformProvider extends OrchidServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @param Dashboard $dashboard
     *
     * @return void
     */
    public function boot(Dashboard $dashboard): void
    {
        parent::boot($dashboard);

        // ...
    }

    /**
     * Register the application menu.
     *
     * @return Menu[]
     */
    public function menu(): array
    {
        return [

            Menu::make('Songs')
                ->icon('disc')
                ->route('platform.app.songs')
                ->permission(PermissionEnum::SONG_INDEX)
                ->title(__('APPLICATION')),
            Menu::make('Songs Metadata')
                ->icon('file-earmark-code-fill')
                ->permission(PermissionEnum::SONG_INDEX)
                ->route('platform.app.song-metadata'),
            Menu::make('Album')
                ->icon('folder2-open')
                ->permission(PermissionEnum::ALBUM_INDEX)
                ->route('platform.app.albums'),
            Menu::make('Artist')
                ->icon('person-badge')
                ->route('platform.app.artists')
                ->permission(PermissionEnum::ARTIST_INDEX),
            Menu::make('Genres')
                ->icon('tag')
                ->route('platform.classification.genres')
                ->title(__('CLASSIFICATION'))
                ->permission(PermissionEnum::CLASSIFYING_INDEX),
            Menu::make('Requests')
                ->icon('building-fill-up')
                ->route('platform.communities.requests')
                ->title(__('COMMUNITIES')),

            Menu::make('FTS Test')
               ->icon('search')
                ->route('platform.systems.tools.query-inspector')
                ->title(__('TOOLS')),

            Menu::make(__('User Inspector'))
                ->icon('bs.search')
                ->route('platform.systems.tools.inspector')
                ->permission(PermissionEnum::SYSTEM_USERS),
//            Menu::make('Get Started')
//                ->icon('bs.book')
//                ->title('Navigation')
//                ->route(config('platform.index')),
//
//            Menu::make('Sample Screen')
//                ->icon('bs.collection')
//                ->route('platform.example')
//                ->badge(fn () => 6),
//
//            Menu::make('Form Elements')
//                ->icon('bs.card-list')
//                ->route('platform.example.fields')
//                ->active('*/examples/form/*'),
//
//            Menu::make('Overview Layouts')
//                ->icon('bs.window-sidebar')
//                ->route('platform.example.layouts'),
//
//            Menu::make('Grid System')
//                ->icon('bs.columns-gap')
//                ->route('platform.example.grid'),
//
//            Menu::make('Charts')
//                ->icon('bs.bar-chart')
//                ->route('platform.example.charts'),
//
//            Menu::make('Cards')
//                ->icon('bs.card-text')
//                ->route('platform.example.cards')
//                ->divider(),

            Menu::make(__('Users'))
                ->icon('bs.people')
                ->route('platform.systems.users')
                ->permission(PermissionEnum::SYSTEM_USERS)
                ->title(__('Access Controls')),

            Menu::make(__('Roles'))
                ->icon('bs.shield')
                ->route('platform.systems.roles')
                ->permission(PermissionEnum::SYSTEM_ROLES)
                ->divider(),

            Menu::make(__('Logs'))
                ->icon('bug')
                ->permission(PermissionEnum::SYSTEM_INFO)
                ->route('platform.logs', ['sort' => '-last_modified'])->title(__('SYSTEM')),

            Menu::make(__('Php Info'))
                ->icon('bug')
                ->permission(PermissionEnum::SYSTEM_INFO)
                ->route('platform.phpinfo', ['sort' => '-last_modified']),

            Menu::make(__('Backup'))
                ->icon('bs.layer-backward')
                ->permission(PermissionEnum::SYSTEM_INFO)
                ->route('platform.backups', ['sort' => '-last_modified']),

            Menu::make('Documentation')
                ->title('Docs')
                ->icon('bs.box-arrow-up-right')
                ->url('https://orchid.software/en/docs')
                ->target('_blank'),

            Menu::make('Changelog')
                ->icon('bs.box-arrow-up-right')
                ->url('https://github.com/orchidsoftware/platform/blob/master/CHANGELOG.md')
                ->target('_blank')
                ->badge(fn () => Dashboard::version(), Color::DARK),
        ];
    }

    /**
     * Register permissions for the application.
     *
     * @return ItemPermission[]
     */
    public function permissions(): array
    {
        return [
            ItemPermission::group('Songs')
                ->addPermission(PermissionEnum::SONG_INDEX, 'View Songs')
                ->addPermission(PermissionEnum::SONG_CREATE, 'Create Songs')
                ->addPermission(PermissionEnum::SONG_EDIT, 'Update Songs')
                ->addPermission(PermissionEnum::SONG_DELETE, 'Delete Songs'),

            ItemPermission::group('Albums')
                ->addPermission(PermissionEnum::ALBUM_INDEX, 'View Albums')
                ->addPermission(PermissionEnum::ALBUM_CREATE, 'Create Albums')
                ->addPermission(PermissionEnum::ALBUM_EDIT, 'Update Albums')
                ->addPermission(PermissionEnum::ALBUM_DELETE, 'Delete Albums'),

            ItemPermission::group('Artists')
                ->addPermission(PermissionEnum::ARTIST_INDEX, 'View Artists')
                ->addPermission(PermissionEnum::ARTIST_CREATE, 'Create Artists')
                ->addPermission(PermissionEnum::ARTIST_EDIT, 'Update Artists')
                ->addPermission(PermissionEnum::ARTIST_DELETE, 'Delete Artists'),

            ItemPermission::group('Classification')
                ->addPermission(PermissionEnum::CLASSIFYING_INDEX, 'View Genres'),


            ItemPermission::group(__('System'))
                ->addPermission('platform.systems.roles', __('Roles'))
                ->addPermission('platform.systems.users', __('Users'))
                ->addPermission(PermissionEnum::SYSTEM_INFO, __('System info')),

        ];
    }
}
