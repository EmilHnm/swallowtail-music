<?php

// @formatter:off
// phpcs:ignoreFile
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
 * @property string $normalized_name
 * @property int $release_year
 * @property string $image_path
 * @property string $type
 * @property string $referer
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
 * @method static \Illuminate\Database\Eloquent\Builder|Album whereNormalizedName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Album whereReferer($value)
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

namespace App\Models\Alt{
/**
 * App\Models\Alt\DatabaseNotificationAlt
 *
 * @property string $id
 * @property string $type
 * @property string $notifiable_type
 * @property int $notifiable_id
 * @property array $data
 * @property \Illuminate\Support\Carbon|null $read_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $notifiable
 * @method static \Illuminate\Database\Eloquent\Builder|DatabaseNotificationAlt advancedFilter($filter_keys = [], $allowed_sorts = [])
 * @method static \Illuminate\Notifications\DatabaseNotificationCollection<int, static> all($columns = ['*'])
 * @method static \Illuminate\Database\Eloquent\Builder|DatabaseNotificationAlt defaultSortBy(string $column, string $direction = 'asc')
 * @method static \Illuminate\Notifications\DatabaseNotificationCollection<int, static> get($columns = ['*'])
 * @method static \Illuminate\Database\Eloquent\Builder|DatabaseNotificationAlt idRange($id, $field = 'id')
 * @method static \Illuminate\Database\Eloquent\Builder|DatabaseNotificationAlt newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DatabaseNotificationAlt newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DatabaseNotificationAlt query()
 * @method static \Illuminate\Database\Eloquent\Builder|DatabaseNotification read()
 * @method static \Illuminate\Database\Eloquent\Builder|DatabaseNotification unread()
 * @method static \Illuminate\Database\Eloquent\Builder|DatabaseNotificationAlt whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DatabaseNotificationAlt whereData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DatabaseNotificationAlt whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DatabaseNotificationAlt whereNotifiableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DatabaseNotificationAlt whereNotifiableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DatabaseNotificationAlt whereReadAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DatabaseNotificationAlt whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DatabaseNotificationAlt whereUpdatedAt($value)
 */
	class DatabaseNotificationAlt extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Artist
 *
 * @property int $id
 * @property string $artist_id
 * @property string $name
 * @property string $normalized_name
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
 * @method static \Illuminate\Database\Eloquent\Builder|Artist advancedFilter($filter_keys = [], $allowed_sorts = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Artist defaultSortBy(string $column, string $direction = 'asc')
 * @method static \Illuminate\Database\Eloquent\Builder|Artist idRange($id, $field = 'id')
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
 * @method static \Illuminate\Database\Eloquent\Builder|Artist whereNormalizedName($value)
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
 * @method static \Illuminate\Database\Eloquent\Builder|Genre advancedFilter($filter_keys = [], $allowed_sorts = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Genre defaultSortBy(string $column, string $direction = 'asc')
 * @method static \Illuminate\Database\Eloquent\Builder|Genre idRange($id, $field = 'id')
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

namespace App\Models\Queue{
/**
 * App\Models\Queue\Job
 *
 * @property int $id
 * @property string $queue
 * @property array $payload
 * @property int $attempts
 * @property int|null $reserved_at
 * @property \Illuminate\Support\Carbon $available_at
 * @property \Illuminate\Support\Carbon $created_at
 * @method static \Illuminate\Database\Eloquent\Builder|Job advancedFilter($filter_keys = [], $allowed_sorts = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Job defaultSortBy(string $column, string $direction = 'asc')
 * @method static \Illuminate\Database\Eloquent\Builder|Job idRange($id, $field = 'id')
 * @method static \Illuminate\Database\Eloquent\Builder|Job newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Job newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Job query()
 * @method static \Illuminate\Database\Eloquent\Builder|Job whereAttempts($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Job whereAvailableAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Job whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Job whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Job wherePayload($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Job whereQueue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Job whereReservedAt($value)
 */
	class Job extends \Eloquent {}
}

namespace App\Models\Queue{
/**
 * App\Models\Queue\Monitor
 *
 * @property int $id
 * @property string|null $job_uuid
 * @property string $job_id
 * @property string|null $name
 * @property string|null $queue
 * @property int $status
 * @property \Illuminate\Support\Carbon|null $queued_at
 * @property \Illuminate\Support\Carbon|null $started_at
 * @property string|null $started_at_exact
 * @property \Illuminate\Support\Carbon|null $finished_at
 * @property string|null $finished_at_exact
 * @property int $attempt
 * @property bool $retried
 * @property int|null $progress
 * @property string|null $exception
 * @property string|null $exception_message
 * @property string|null $exception_class
 * @property string|null $data
 * @method static \Illuminate\Database\Eloquent\Builder|Monitor advancedFilter($filter_keys = [], $allowed_sorts = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Monitor defaultSortBy(string $column, string $direction = 'asc')
 * @method static \Illuminate\Database\Eloquent\Builder|Monitor failed()
 * @method static \Illuminate\Database\Eloquent\Builder|Monitor idRange($id, $field = 'id')
 * @method static \Illuminate\Database\Eloquent\Builder|Monitor lastHour()
 * @method static \Illuminate\Database\Eloquent\Builder|Monitor newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Monitor newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Monitor ordered()
 * @method static \Illuminate\Database\Eloquent\Builder|Monitor query()
 * @method static \Illuminate\Database\Eloquent\Builder|Monitor succeeded()
 * @method static \Illuminate\Database\Eloquent\Builder|Monitor today()
 * @method static \Illuminate\Database\Eloquent\Builder|Monitor whereAttempt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Monitor whereData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Monitor whereException($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Monitor whereExceptionClass($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Monitor whereExceptionMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Monitor whereFinishedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Monitor whereFinishedAtExact($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Monitor whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Monitor whereJob($jobId)
 * @method static \Illuminate\Database\Eloquent\Builder|Monitor whereJobId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Monitor whereJobUuid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Monitor whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Monitor whereProgress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Monitor whereQueue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Monitor whereQueuedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Monitor whereRetried($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Monitor whereStartedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Monitor whereStartedAtExact($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Monitor whereStatus($value)
 */
	class Monitor extends \Eloquent {}
}

namespace App\Models\Queue{
/**
 * App\Models\Queue\QueueFailedJob
 *
 * @property int $id
 * @property string $uuid
 * @property string $connection
 * @property string $queue
 * @property array $payload
 * @property string $exception
 * @property \Illuminate\Support\Carbon $failed_at
 * @method static \Illuminate\Database\Eloquent\Builder|QueueFailedJob advancedFilter($filter_keys = [], $allowed_sorts = [])
 * @method static \Illuminate\Database\Eloquent\Builder|QueueFailedJob defaultSortBy(string $column, string $direction = 'asc')
 * @method static \Illuminate\Database\Eloquent\Builder|QueueFailedJob idRange($id, $field = 'id')
 * @method static \Illuminate\Database\Eloquent\Builder|QueueFailedJob newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|QueueFailedJob newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|QueueFailedJob query()
 * @method static \Illuminate\Database\Eloquent\Builder|QueueFailedJob whereConnection($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QueueFailedJob whereException($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QueueFailedJob whereFailedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QueueFailedJob whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QueueFailedJob wherePayload($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QueueFailedJob whereQueue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QueueFailedJob whereUuid($value)
 */
	class QueueFailedJob extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Request
 *
 * @property int $id
 * @property \App\Models\User|null $requester
 * @property string $type
 * @property string $status
 * @property mixed $body
 * @property string|null $filled_by
 * @property int $user_fillable
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User|null $filledBy
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Response> $responses
 * @property-read int|null $responses_count
 * @method static \Illuminate\Database\Eloquent\Builder|Request advancedFilter($filter_keys = [], $allowed_sorts = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Request defaultSortBy(string $column, string $direction = 'asc')
 * @method static \Illuminate\Database\Eloquent\Builder|Request idRange($id, $field = 'id')
 * @method static \Illuminate\Database\Eloquent\Builder|Request newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Request newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Request query()
 * @method static \Illuminate\Database\Eloquent\Builder|Request whereBody($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Request whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Request whereFilledBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Request whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Request whereRequester($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Request whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Request whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Request whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Request whereUserFillable($value)
 */
	class Request extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Response
 *
 * @property int $id
 * @property int $request_id
 * @property \App\Models\User|null $responder
 * @property string $content
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Request $request
 * @method static \Illuminate\Database\Eloquent\Builder|Response advancedFilter($filter_keys = [], $allowed_sorts = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Response defaultSortBy(string $column, string $direction = 'asc')
 * @method static \Illuminate\Database\Eloquent\Builder|Response idRange($id, $field = 'id')
 * @method static \Illuminate\Database\Eloquent\Builder|Response newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Response newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Response query()
 * @method static \Illuminate\Database\Eloquent\Builder|Response whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Response whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Response whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Response whereRequestId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Response whereResponder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Response whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Response whereUpdatedAt($value)
 */
	class Response extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Song
 *
 * @property int $id
 * @property string $song_id
 * @property string $user_id
 * @property string|null $album_id
 * @property string $title
 * @property string $normalized_title
 * @property int $listens
 * @property string $display
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
 * @method static \Illuminate\Database\Eloquent\Builder|Song whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Song whereListens($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Song whereNormalizedTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Song whereSongId($value)
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
 * @property int $duration
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
 * @method static \Illuminate\Database\Eloquent\Builder|SongMetadata whereDuration($value)
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
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $gender
 * @property string|null $profile_photo_url
 * @property string|null $dob
 * @property string|null $region
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property array|null $permissions
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Song> $likes
 * @property-read int|null $likes_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Playlist> $playlists
 * @property-read int|null $playlists_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Orchid\Platform\Models\Role> $roles
 * @property-read int|null $roles_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Album> $uploaded_albums
 * @property-read int|null $uploaded_albums_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Song> $uploaded_songs
 * @property-read int|null $uploaded_songs_count
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
 * @method static \Illuminate\Database\Eloquent\Builder|User onlyTrashed()
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
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|User withoutTrashed()
 */
	class User extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\WebStatistic
 *
 * @property int $id
 * @property int $total_users
 * @property int $total_sessions
 * @property int $total_sessions_duration
 * @property int $total_requests
 * @property int $total_songs
 * @property int $total_user_upload_songs
 * @property int $total_played_time
 * @property int $total_played_duration
 * @property int $total_albums
 * @property int $total_artists
 * @property int $total_playlists
 * @property int $total_genres
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|WebStatistic newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WebStatistic newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WebStatistic query()
 * @method static \Illuminate\Database\Eloquent\Builder|WebStatistic whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WebStatistic whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WebStatistic whereTotalAlbums($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WebStatistic whereTotalArtists($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WebStatistic whereTotalGenres($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WebStatistic whereTotalPlayedDuration($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WebStatistic whereTotalPlayedTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WebStatistic whereTotalPlaylists($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WebStatistic whereTotalRequests($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WebStatistic whereTotalSessions($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WebStatistic whereTotalSessionsDuration($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WebStatistic whereTotalSongs($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WebStatistic whereTotalUserUploadSongs($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WebStatistic whereTotalUsers($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WebStatistic whereUpdatedAt($value)
 */
	class WebStatistic extends \Eloquent {}
}

