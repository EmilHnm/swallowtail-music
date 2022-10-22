<?php

use App\Http\Controllers\AlbumController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\GenreController;
use App\Http\Controllers\ArtistController;
use App\Http\Controllers\PlaylistController;
use App\Http\Controllers\SongController;

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

Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/user', [AuthController::class, 'user']);
    });

    Route::prefix('artist')->group(function () {
        Route::get('/{id}', [ArtistController::class, 'show']);
        Route::get('/', [ArtistController::class, 'getAll']);
    });

    Route::prefix('genre')->group(function () {
        Route::get('/{id}', [GenreController::class, 'show']);
        Route::get('/', [GenreController::class, 'getAll']);
    });

    Route::prefix('song')->group(function () {
        Route::post('/upload', [SongController::class, 'uploadSong']);
    });

    Route::prefix('album')->group(function () {
        Route::post('/upload', [AlbumController::class, 'uploadAlbum']);
    });

    Route::prefix('playlist')->group(function () {
        Route::post('/create', [PlaylistController::class, 'createPlaylist']);
        Route::get('/all', [PlaylistController::class, 'authPlaylist']);
        Route::get('/collection', [PlaylistController::class, 'authLikedList']);
        Route::get('/{id}', [PlaylistController::class, 'getPlaylist']);
        Route::get('/{id}/song', [PlaylistController::class, 'getPlaylistSong']);
        Route::delete('/{id}/delete', [PlaylistController::class, 'deletePlaylistSong']);
    });
});
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
});
