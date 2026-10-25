<?php

namespace App\Contracts\Movie;

use App\Contracts\BaseRepositoryInterface;
use App\Models\Movie;
use Illuminate\Pagination\LengthAwarePaginator;

interface MovieRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Filter genres based on the given criteria.
     *
     * @param array $filters
     * @param string $sortBy
     * @param string $order
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function filter(array $filters, string $sortBy = 'title', string $order = 'asc', int $perPage = 15): LengthAwarePaginator;

    /**
     * Sync the genres for the given movie.
     *
     * @param Movie $movie
     * @param array $genreIds
     * @return void
     */
    public function syncGenres(Movie $movie, array $genreIds): void;
}
