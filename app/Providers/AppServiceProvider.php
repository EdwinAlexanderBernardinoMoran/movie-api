<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Models\Genre;
use App\Models\Movie;
use App\Observers\GenreObserver;
use App\Observers\MovieObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Genre::observe(GenreObserver::class);
        Movie::observe(MovieObserver::class);
    }
}
