<?php

namespace App\Contracts\Movie;

use Illuminate\Database\Eloquent\Collection;

use App\Contracts\BaseRepositoryInterface;
use App\Models\Movie;

interface MovieRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Filter genres based on the given criteria.
     *
     * @param array $filters
     * @param string $sortBy
     * @param string $order
     * @return Collection
     */
    public function filter(array $filters, string $sortBy = 'title', string $order = 'asc'): Collection;

    /**
     * Sync the genres for the given movie.
     *
     * @param Movie $movie
     * @param array $genreIds
     * @return void
     */
    public function syncGenres(Movie $movie, array $genreIds): void;
}
