<?php

namespace App\Providers;

use App\Contracts\Genre\GenreRepositoryInterface;
use App\Repositories\Genre\GenreRepository;
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
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
