<?php

declare(strict_types=1);

use App\Http\Controllers\Albums\CreateAlbumController;
use App\Http\Controllers\Albums\DeleteAlbumController;
use App\Http\Controllers\Albums\RestoreAlbumController;
use App\Http\Controllers\Albums\ShowAlbumByDirectSlugController;
use App\Http\Controllers\Albums\ShowAlbumBySlugsController;
use App\Http\Controllers\Albums\UpdateAlbumController;
use App\Http\Controllers\Users\DeleteUserController;
use App\Http\Controllers\Users\RestoreUserController;
use App\Http\Controllers\Users\ShowMeController;
use App\Http\Controllers\Users\ShowUserController;
use App\Http\Controllers\Users\UpdateUserController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;

Route::get('/', fn() => new JsonResponse(
    data: ',,, =^..^= ,,,',
    status: Response::HTTP_OK,
))->middleware(['throttle:api-get'])->name('home');

// Authentication routes

Route::prefix('auth')->group(base_path(path: 'routes/api/auth/routesAuth.php'));

// Public

Route::middleware(['throttle:api-get'])->group(static function (): void {
    // User info with albums
    Route::get('/users/{user_slug}', ShowUserController::class)
        ->name('users.show');
    // Album info
    Route::get('/albums/{user_slug}/{album_slug}', ShowAlbumBySlugsController::class)
        ->name('albums.show');
    // Direct slug album info (bypass access settings)
    Route::get('/direct/{direct_access_slug}', ShowAlbumByDirectSlugController::class)
        ->name('albums.direct');
});

// Authenticated

Route::middleware(['auth.required'])->group(static function (): void {
    // Current authenticated user info
    Route::get('/me', ShowMeController::class)
        ->middleware(['throttle:api-get'])
        ->name('me');

    // Update user
    Route::put('/users/{user_id}', UpdateUserController::class)
        ->middleware(['throttle:api-modify'])
        ->name('user.update');
    // Delete user
    Route::delete('/users/{user_id}', DeleteUserController::class)
        ->middleware(['throttle:api-modify'])
        ->name('user.delete');
    // Restore user
    Route::post('/users/{user_id}/restore', RestoreUserController::class)
        ->middleware(['throttle:api-modify'])
        ->name('user.restore');

    // Create new album
    Route::post('/albums', CreateAlbumController::class)
        ->middleware(['throttle:api-modify'])
        ->name('album.create');
    // Update album
    Route::put('/albums/{album_id}', UpdateAlbumController::class)
        ->middleware(['throttle:api-modify'])
        ->name('album.update');
    // Delete album
    Route::delete('/albums/{album_id}', DeleteAlbumController::class)
        ->middleware(['throttle:api-modify'])
        ->name('album.delete');
    // Restore album
    Route::post('/albums/{album_id}/restore', RestoreAlbumController::class)
        ->middleware(['throttle:api-modify'])
        ->name('album.restore');
});

// Preflight routes

Route::options('{any}', fn() => response()->noContent())->where('any', '.*')
    ->middleware(['throttle:api-get'])
    ->name('preflight');
