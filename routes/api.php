<?php

use Monolog\Logger;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SongController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AlbumController;
use App\Http\Controllers\GenreController;
use App\Http\Controllers\ArtistController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\PlaylistController;
use App\Http\Controllers\AlbumAdminController;
use App\Http\Controllers\ArtistAdminController;
use App\Http\Controllers\StaticAdminController;
use App\Http\Controllers\admin\SongAdminController;
use App\Http\Controllers\admin\UserAdminController;
use Rap2hpoutre\LaravelLogViewer\LogViewerController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware("auth:sanctum")->group(function () {
    Route::prefix("auth")->group(function () {
        Route::post("/logout", [AuthController::class, "logout"]);
        Route::post("/logoutAll", [AuthController::class, "logoutAllDevice"]);
        Route::get("/user", [AuthController::class, "user"]);
        Route::post("/verify-email", [
            AuthController::class,
            "verificationEmail",
        ]);
    });

    Route::prefix("artist")->group(function () {
        Route::get("/", [ArtistController::class, "getAll"]);
        Route::get("/top", [ArtistController::class, "getTop"]);
        Route::get("/search", [ArtistController::class, "searchArtist"]);
        Route::get("/album/{id}", [
            ArtistController::class,
            "getAlbumByArtistId",
        ]);
        Route::get("/song/{id}", [
            ArtistController::class,
            "getSongByArtistId",
        ]);
        Route::get("/song/top/{id}", [
            ArtistController::class,
            "getTopSongByArtistId",
        ]);
        Route::get("/{id}", [ArtistController::class, "show"]);
    });

    Route::prefix("genre")->group(function () {
        Route::get("/{id}", [GenreController::class, "show"]);
        Route::get("/", [GenreController::class, "getAll"]);
    });

    Route::prefix("song")->group(function () {
        Route::post("/upload", [
            SongController::class,
            "uploadSong",
        ])->middleware(["verified"]);
        Route::get("/latest", [SongController::class, "latestSong"]);
        Route::get("/search", [SongController::class, "searchSong"]);
        Route::get("/{id}", [SongController::class, "getSongInfo"]);
        Route::get("/{id}/play", [SongController::class, "getSongForPlay"]);
        Route::middleware("requestTimeDelay")->post("/{id}/listens", [
            SongController::class,
            "increaseSongListens",
        ]);
        Route::post("/{id}/like", [SongController::class, "likeSong"]);
        Route::get("/{id}/liked", [SongController::class, "likedSong"]);
        Route::delete("/{id}/delete", [SongController::class, "deleteSong"]);
    });

    Route::prefix("album")->group(function () {
        Route::post("/upload", [
            AlbumController::class,
            "uploadAlbum",
        ])->middleware(["verified"]);
        Route::get("/search", [AlbumController::class, "searchAlbum"]);
        Route::get("/latest", [AlbumController::class, "getLatestAlbum"]);
        Route::get("/top", [AlbumController::class, "getTopAlbum"]);
        Route::get("/{id}", [AlbumController::class, "getAlbumInfo"]);
        Route::get("/{id}/song", [AlbumController::class, "getAlbumSongs"]);
    });
    Route::prefix("playlist")->group(function () {
        Route::post("/create", [PlaylistController::class, "createPlaylist"]);
        Route::get("/all", [PlaylistController::class, "authPlaylist"]);
        Route::get("/collection", [PlaylistController::class, "authLikedList"]);
        Route::post("/set-type", [
            PlaylistController::class,
            "settypePlaylist",
        ]);
        Route::post("/song-add", [
            PlaylistController::class,
            "addSongToPlaylist",
        ]);
        Route::post("/album-add", [
            PlaylistController::class,
            "addAlbumToPlaylist",
        ]);
        Route::post("/update", [PlaylistController::class, "updatePlaylist"]);
        Route::post("/add-to-playlist", [
            PlaylistController::class,
            "addToPlaylist",
        ]);
        Route::get("/{id}", [PlaylistController::class, "getPlaylist"]);
        Route::get("/{id}/song", [
            PlaylistController::class,
            "getPlaylistSong",
        ]);
        Route::get("/{id}/addable", [
            PlaylistController::class,
            "getAddableListSong",
        ]);
        Route::delete("/{id}/delete", [
            PlaylistController::class,
            "deletePlaylist",
        ]);
    });

    Route::prefix("user")->group(function () {
        Route::get("/search", [UserController::class, "searchUser"]);
        Route::get("/{id}", [UserController::class, "show"]);
        Route::get("/{id}/top-artists", [
            UserController::class,
            "getUserTopArtist",
        ]);
        Route::get("/{id}/public-playlist", [
            UserController::class,
            "getPublicPlaylist",
        ]);
        Route::get("/{id}/top-tracks", [UserController::class, "getTopTracks"]);
    });

    Route::prefix("account")->group(function () {
        Route::post("/update", [AccountController::class, "updateAccount"]);
        Route::post("/profile-image", [
            AccountController::class,
            "updateProfilePicture",
        ]);
        Route::post("/password", [AccountController::class, "updatePassword"]);
        //Album control
        Route::prefix("album")->group(function () {
            Route::get("/uploaded", [
                AlbumController::class,
                "getUploadedAlbum",
            ]);
            Route::post("/update", [AlbumController::class, "updateAlbum"]);
            Route::get("/addable", [
                AlbumController::class,
                "getSongNotInAlbum",
            ]);
            Route::post("/song-remove", [
                AlbumController::class,
                "removeAlbumSong",
            ]);
            Route::post("/song-add", [AlbumController::class, "addAlbumSong"]);
            Route::delete("/{id}/delete", [
                AlbumController::class,
                "deleteAlbum",
            ]);
        });
        //Song control
        Route::prefix("song")->group(function () {
            Route::post("/update", [SongController::class, "updateSong"]);
            Route::get("/uploaded", [SongController::class, "uploadedSong"]);
            Route::delete("/{id}/delete", [
                SongController::class,
                "deleteSong",
            ]);
        });
    });

    Route::prefix("admin")
        ->middleware(["verified", "moderatorAccess"])
        ->group(function () {
            Route::prefix("users")->group(function () {
                Route::get("/", [UserAdminController::class, "getAll"]);
                Route::get("/{id}", [UserAdminController::class, "show"]);
                Route::get("/{id}/songs", [
                    UserAdminController::class,
                    "showUserUploadSong",
                ]);
                Route::get("/{id}/albums", [
                    UserAdminController::class,
                    "showUserUploadAlbum",
                ]);
                Route::post("/{id}/role/update", [
                    UserAdminController::class,
                    "updateRole",
                ])->middleware(["adminAccess"]);
                Route::delete("/{id}/delete", [
                    UserAdminController::class,
                    "deleteUser",
                ])->middleware(["adminAccess"]);
            });

            Route::prefix("songs")->group(function () {
                Route::get("/", [SongAdminController::class, "getAll"]);
                Route::post("/update", [SongAdminController::class, "update"]);
                Route::get("/{id}", [SongAdminController::class, "show"]);
                Route::delete("/{id}/delete", [
                    SongAdminController::class,
                    "delete",
                ]);
            });

            Route::prefix("albums")->group(function () {
                Route::get("/", [AlbumAdminController::class, "getAll"]);
                Route::post("/update", [AlbumAdminController::class, "update"]);
                Route::post("/song-remove", [
                    AlbumAdminController::class,
                    "songRemove",
                ]);
                Route::post("/song-add", [
                    AlbumAdminController::class,
                    "songAdd",
                ]);
                Route::get("/addable", [
                    AlbumAdminController::class,
                    "getSongNotInAlbum",
                ]);
                Route::get("/{id}", [AlbumAdminController::class, "show"]);
                Route::get("/{id}/song", [AlbumAdminController::class, "song"]);
                Route::delete("/{id}/delete", [
                    AlbumAdminController::class,
                    "delete",
                ]);
            });

            Route::prefix("artists")->group(function () {
                Route::get("/", [ArtistAdminController::class, "getAll"]);
                Route::post("/update", [
                    ArtistAdminController::class,
                    "update",
                ]);
                Route::get("/{id}", [ArtistAdminController::class, "show"]);
                Route::delete("/{id}/delete", [
                    ArtistAdminController::class,
                    "delete",
                ]);
            });
            Route::prefix("static")->group(function () {
                Route::get("/logs", [LogViewerController::class, "index"]);
            });
        });
});
Route::prefix("auth")->group(function () {
    Route::post("/register", [AuthController::class, "register"])->name(
        "register"
    );
    Route::post("/login", [AuthController::class, "login"])->name("login");
    Route::post("/forgot-password", [AuthController::class, "forgotPassword"]);
    Route::post("/reset-password", [AuthController::class, "resetPassword"]);
    Route::get("/verify-email", [AuthController::class, "verifyEmail"]);
});
