<?php

namespace App\Enum;

use MyCLabs\Enum\Enum;

class PermissionEnum extends Enum
{
    const MAIN = 'platform.index';

    const SYSTEM_ROLES = 'platform.systems.roles';
    const SYSTEM_USERS = 'platform.systems.users';

    const SYSTEM_INFO = 'platform.info';

    const SONG_INDEX = 'platform.songs.index';
    const SONG_EDIT = 'platform.songs.edit';
    const SONG_CREATE = 'platform.songs.create';
    const SONG_DELETE = 'platform.songs.delete';

    const ARTIST_INDEX = 'platform.artists.index';
    const ARTIST_EDIT = 'platform.artists.edit';
    const ARTIST_CREATE = 'platform.artists.create';
    const ARTIST_DELETE = 'platform.artists.delete';

    const ALBUM_INDEX = 'platform.albums.index';
    const ALBUM_EDIT = 'platform.albums.edit';
    const ALBUM_CREATE = 'platform.albums.create';
    const ALBUM_DELETE = 'platform.albums.delete';

    const CLASSIFYING_INDEX = 'platform.genres.index';

    public static function getMiddleware($keys) {
        return array_map(function ($key) {
            return 'access:' . $key;
        }, $keys);
    }
}
