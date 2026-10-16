<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\V1\GenreController;
use App\Http\Controllers\Api\V1\HealthController;
use App\Http\Controllers\Api\V1\MovieController;

Route::get('health', HealthController::class);

Route::apiResource('movies', MovieController::class);
Route::apiResource('genres', GenreController::class);

Route::get('genres/slug/{slug}', [GenreController::class, 'showBySlug'])->name('genres.showBySlug');
Route::post('genres/restore/{id}', [GenreController::class, 'restore'])->name('genres.restore');
