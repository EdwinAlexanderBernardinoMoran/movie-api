<?php

namespace App\Providers;

use App\Contracts\Genre\GenreRepositoryInterface;
use App\Contracts\Movie\MovieRepositoryInterface;

use App\Repositories\Genre\GenreRepository;
use App\Repositories\Movie\MovieRepository;

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            GenreRepositoryInterface::class, GenreRepository::class
        );
         $this->app->bind(
            MovieRepositoryInterface::class, MovieRepository::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
