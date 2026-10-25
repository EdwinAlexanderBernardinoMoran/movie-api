<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\GenreController;
use App\Http\Controllers\Api\V1\HealthController;
use App\Http\Controllers\Api\V1\MovieController;
use Illuminate\Support\Facades\Route;

$genreRoute = 'genres/{genre}';
$movieRoute = 'movies/{movie}';

Route::prefix('v1')->group(function () use ($genreRoute, $movieRoute) {
    Route::get('health', HealthController::class);

    /**
     * Authentication routes for the API.
     */
    Route::prefix('auth')->group(function () {
        Route::post('register', [AuthController::class, 'register'])->name('auth.register');
        Route::post('login', [AuthController::class, 'login'])->name('auth.login');
    });

    /**
     * Genre routes for the API.
     */
    Route::get('genres', [GenreController::class, 'index'])->name('genres.index');
    Route::get('genres/slug/{slug}', [GenreController::class, 'showBySlug'])->name('genres.showBySlug');
    Route::get($genreRoute, [GenreController::class, 'show'])->name('genres.show');

    /**
     * Movie routes for the API.
     */
    Route::get('movies', [MovieController::class, 'index'])->name('movies.index');
    Route::get($movieRoute, [MovieController::class, 'show'])->name('movies.show');

    /**
     * Protected authentication routes for the API.
     */
    Route::middleware('auth:api')->group(function () use ($genreRoute, $movieRoute) {
        Route::prefix('auth')->group(function () {
            Route::post('logout', [AuthController::class, 'logout'])->name('auth.logout');
            Route::post('refresh', [AuthController::class, 'refresh'])->name('auth.refresh');
            Route::get('me', [AuthController::class, 'me'])->name('auth.me');
        });

        // Genre management routes for admin and editor roles
        Route::middleware('role:admin,editor')->group(function () use ($genreRoute) {
            Route::post('genres', [GenreController::class, 'store'])->name('genres.store');
            Route::put($genreRoute, [GenreController::class, 'update'])->name('genres.update');
            Route::patch($genreRoute, [GenreController::class, 'update'])->name('genres.patch');
        });

        Route::middleware('role:admin')->group(function () use ($genreRoute) {
            Route::delete($genreRoute, [GenreController::class, 'destroy'])->name('genres.destroy');
            Route::post('genres/restore/{id}', [GenreController::class, 'restore'])->name('genres.restore');
        });

        // Movie management routes for admin and editor roles
        Route::middleware('role:admin,editor')->group(function () use ($movieRoute) {
            Route::post('movies', [MovieController::class, 'store'])->name('movies.store');
            Route::put($movieRoute, [MovieController::class, 'update'])->name('movies.update');
            Route::patch($movieRoute, [MovieController::class, 'update'])->name('movies.patch');
        });

        Route::middleware('role:admin')->group(function () use ($movieRoute) {
            Route::delete($movieRoute, [MovieController::class, 'destroy'])->name('movies.destroy');
        });
    });
});
