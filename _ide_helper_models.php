<?php

// @formatter:off
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * App\Models\Album
 *
 * @property int $id
 * @property string $album_id
 * @property string $user_id
 * @property string $name
 * @property int $release_year
 * @property string $image_path
 * @property string $type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Genre> $genre
 * @property-read int|null $genre_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Song> $song
 * @property-read int|null $song_count
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|Album advancedFilter($filter_keys = [], $allowed_sorts = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Album defaultSortBy(string $column, string $direction = 'asc')
 * @method static \Illuminate\Database\Eloquent\Builder|Album idRange($id, $field = 'id')
 * @method static \Illuminate\Database\Eloquent\Builder|Album newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Album newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Album query()
 * @method static \Illuminate\Database\Eloquent\Builder|Album whereAlbumId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Album whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Album whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Album whereImagePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Album whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Album whereReleaseYear($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Album whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Album whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Album whereUserId($value)
 */
	class Album extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\AlbumGenre
 *
 * @property int $id
 * @property string $album_id
 * @property string $genre_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|AlbumGenre newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AlbumGenre newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AlbumGenre query()
 * @method static \Illuminate\Database\Eloquent\Builder|AlbumGenre whereAlbumId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AlbumGenre whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AlbumGenre whereGenreId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AlbumGenre whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AlbumGenre whereUpdatedAt($value)
 */
	class AlbumGenre extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Artist
 *
 * @property int $id
 * @property string $artist_id
 * @property string $name
 * @property string|null $description
 * @property string|null $image_path
 * @property string|null $banner_path
 * @property int $listens
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Genre> $genres
 * @property-read int|null $genres_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Song> $song
 * @property-read int|null $song_count
 * @method static \Illuminate\Database\Eloquent\Builder|Artist newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Artist newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Artist query()
 * @method static \Illuminate\Database\Eloquent\Builder|Artist whereArtistId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Artist whereBannerPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Artist whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Artist whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Artist whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Artist whereImagePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Artist whereListens($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Artist whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Artist whereUpdatedAt($value)
 */
	class Artist extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\ArtistGenre
 *
 * @property int $id
 * @property string $artist_id
 * @property string $genre_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|ArtistGenre newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ArtistGenre newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ArtistGenre query()
 * @method static \Illuminate\Database\Eloquent\Builder|ArtistGenre whereArtistId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ArtistGenre whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ArtistGenre whereGenreId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ArtistGenre whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ArtistGenre whereUpdatedAt($value)
 */
	class ArtistGenre extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EmailVerificationToken
 *
 * @property int $id
 * @property string $user_id
 * @property string $token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|EmailVerificationToken newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EmailVerificationToken newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EmailVerificationToken query()
 * @method static \Illuminate\Database\Eloquent\Builder|EmailVerificationToken whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EmailVerificationToken whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EmailVerificationToken whereToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EmailVerificationToken whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EmailVerificationToken whereUserId($value)
 */
	class EmailVerificationToken extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Genre
 *
 * @property int $id
 * @property string|null $genre_id
 * @property string $name
 * @property string $slug
 * @property string|null $description
 * @property string|null $ref
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Album> $album
 * @property-read int|null $album_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Artist> $artist
 * @property-read int|null $artist_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Song> $song
 * @property-read int|null $song_count
 * @method static \Illuminate\Database\Eloquent\Builder|Genre newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Genre newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Genre query()
 * @method static \Illuminate\Database\Eloquent\Builder|Genre whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Genre whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Genre whereGenreId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Genre whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Genre whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Genre whereRef($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Genre whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Genre whereUpdatedAt($value)
 */
	class Genre extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\LikedSong
 *
 * @property int $id
 * @property string $user_id
 * @property string $song_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Song|null $song
 * @method static \Illuminate\Database\Eloquent\Builder|LikedSong newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LikedSong newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LikedSong query()
 * @method static \Illuminate\Database\Eloquent\Builder|LikedSong whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LikedSong whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LikedSong whereSongId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LikedSong whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LikedSong whereUserId($value)
 */
	class LikedSong extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\PasswordReset
 *
 * @property int $id
 * @property string $user_id
 * @property string $token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|PasswordReset newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PasswordReset newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PasswordReset query()
 * @method static \Illuminate\Database\Eloquent\Builder|PasswordReset whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PasswordReset whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PasswordReset whereToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PasswordReset whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PasswordReset whereUserId($value)
 */
	class PasswordReset extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Playlist
 *
 * @property int $id
 * @property string|null $playlist_id
 * @property string|null $user_id
 * @property string|null $title
 * @property string|null $image_path
 * @property string|null $description
 * @property string|null $type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Song> $song
 * @property-read int|null $song_count
 * @method static \Illuminate\Database\Eloquent\Builder|Playlist newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Playlist newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Playlist query()
 * @method static \Illuminate\Database\Eloquent\Builder|Playlist whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Playlist whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Playlist whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Playlist whereImagePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Playlist wherePlaylistId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Playlist whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Playlist whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Playlist whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Playlist whereUserId($value)
 */
	class Playlist extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\PlaylistSong
 *
 * @property int $id
 * @property string|null $playlist_id
 * @property string|null $song_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Song> $song
 * @property-read int|null $song_count
 * @method static \Illuminate\Database\Eloquent\Builder|PlaylistSong newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PlaylistSong newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PlaylistSong query()
 * @method static \Illuminate\Database\Eloquent\Builder|PlaylistSong whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PlaylistSong whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PlaylistSong wherePlaylistId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PlaylistSong whereSongId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PlaylistSong whereUpdatedAt($value)
 */
	class PlaylistSong extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Song
 *
 * @property int $id
 * @property string $song_id
 * @property string|null $user_id
 * @property string|null $album_id
 * @property string|null $title
 * @property string|null $sub_title
 * @property int|null $listens
 * @property int|null $duration
 * @property string|null $display
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Album|null $album
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Artist> $artist
 * @property-read int|null $artist_count
 * @property-read \App\Models\SongMetadata|null $file
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Genre> $genre
 * @property-read int|null $genre_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Playlist> $playlist
 * @property-read int|null $playlist_count
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|Song advancedFilter($filter_keys = [], $allowed_sorts = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Song defaultSortBy(string $column, string $direction = 'asc')
 * @method static \Illuminate\Database\Eloquent\Builder|Song idRange($id, $field = 'id')
 * @method static \Illuminate\Database\Eloquent\Builder|Song newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Song newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Song query()
 * @method static \Illuminate\Database\Eloquent\Builder|Song whereAlbumId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Song whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Song whereDisplay($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Song whereDuration($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Song whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Song whereListens($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Song whereSongId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Song whereSubTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Song whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Song whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Song whereUserId($value)
 */
	class Song extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\SongArtist
 *
 * @property int $id
 * @property string $song_id
 * @property string $artist_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|SongArtist newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SongArtist newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SongArtist query()
 * @method static \Illuminate\Database\Eloquent\Builder|SongArtist whereArtistId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SongArtist whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SongArtist whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SongArtist whereSongId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SongArtist whereUpdatedAt($value)
 */
	class SongArtist extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\SongFile
 *
 * @property int $id
 * @property string $song_id
 * @property string|null $file_path
 * @property string|null $driver
 * @property string|null $status
 * @property mixed|null $lyrics
 * @property int $referer
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|SongFile newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SongFile newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SongFile query()
 * @method static \Illuminate\Database\Eloquent\Builder|SongFile whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SongFile whereDriver($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SongFile whereFilePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SongFile whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SongFile whereLyrics($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SongFile whereReferer($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SongFile whereSongId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SongFile whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SongFile whereUpdatedAt($value)
 */
	class SongFile extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\SongGenre
 *
 * @property int $id
 * @property string $song_id
 * @property string $genre_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Genre|null $genre
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Song> $song
 * @property-read int|null $song_count
 * @method static \Illuminate\Database\Eloquent\Builder|SongGenre newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SongGenre newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SongGenre query()
 * @method static \Illuminate\Database\Eloquent\Builder|SongGenre whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SongGenre whereGenreId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SongGenre whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SongGenre whereSongId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SongGenre whereUpdatedAt($value)
 */
	class SongGenre extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\SongMetadata
 *
 * @property int $id
 * @property string $song_id
 * @property string|null $file_path
 * @property string|null $driver
 * @property string|null $status
 * @property int|null $size
 * @property string|null $hash
 * @property mixed|null $lyrics
 * @property int $referer
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Song|null $song
 * @method static \Illuminate\Database\Eloquent\Builder|SongMetadata advancedFilter($filter_keys = [], $allowed_sorts = [])
 * @method static \Illuminate\Database\Eloquent\Builder|SongMetadata defaultSortBy(string $column, string $direction = 'asc')
 * @method static \Illuminate\Database\Eloquent\Builder|SongMetadata idRange($id, $field = 'id')
 * @method static \Illuminate\Database\Eloquent\Builder|SongMetadata newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SongMetadata newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SongMetadata query()
 * @method static \Illuminate\Database\Eloquent\Builder|SongMetadata whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SongMetadata whereDriver($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SongMetadata whereFilePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SongMetadata whereHash($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SongMetadata whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SongMetadata whereLyrics($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SongMetadata whereReferer($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SongMetadata whereSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SongMetadata whereSongId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SongMetadata whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SongMetadata whereUpdatedAt($value)
 */
	class SongMetadata extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\User
 *
 * @property int $id
 * @property string $name
 * @property string $user_id
 * @property string $email
 * @property string|null $role
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $gender
 * @property string|null $profile_photo_url
 * @property string|null $dob
 * @property string|null $region
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property array|null $permissions
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Orchid\Platform\Models\Role> $roles
 * @property-read int|null $roles_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \Illuminate\Database\Eloquent\Builder|User averageByDays(string $value, $startDate = null, $stopDate = null, ?string $dateColumn = null)
 * @method static \Illuminate\Database\Eloquent\Builder|User byAccess(string $permitWithoutWildcard)
 * @method static \Illuminate\Database\Eloquent\Builder|User byAnyAccess($permitsWithoutWildcard)
 * @method static \Illuminate\Database\Eloquent\Builder|User countByDays($startDate = null, $stopDate = null, ?string $dateColumn = null)
 * @method static \Illuminate\Database\Eloquent\Builder|User countForGroup(string $groupColumn)
 * @method static \Illuminate\Database\Eloquent\Builder|User defaultSort(string $column, string $direction = 'asc')
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|User filters(?mixed $kit = null, ?\Orchid\Filters\HttpFilter $httpFilter = null)
 * @method static \Illuminate\Database\Eloquent\Builder|User filtersApply(iterable $filters = [])
 * @method static \Illuminate\Database\Eloquent\Builder|User filtersApplySelection($class)
 * @method static \Illuminate\Database\Eloquent\Builder|User maxByDays(string $value, $startDate = null, $stopDate = null, ?string $dateColumn = null)
 * @method static \Illuminate\Database\Eloquent\Builder|User minByDays(string $value, $startDate = null, $stopDate = null, ?string $dateColumn = null)
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 * @method static \Illuminate\Database\Eloquent\Builder|User sumByDays(string $value, $startDate = null, $stopDate = null, ?string $dateColumn = null)
 * @method static \Illuminate\Database\Eloquent\Builder|User valuesByDays(string $value, $startDate = null, $stopDate = null, string $dateColumn = 'created_at')
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereDob($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereGender($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePermissions($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereProfilePhotoUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRegion($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUserId($value)
 */
	class User extends \Eloquent {}
}

