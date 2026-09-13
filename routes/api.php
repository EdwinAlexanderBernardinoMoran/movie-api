<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\V1\GenreController;
use App\Http\Controllers\Api\V1\HealthController;

Route::get('health', HealthController::class);
Route::apiResource('genres', GenreController::class);
