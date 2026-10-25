<?php

namespace App\Repositories\Movie;

use Illuminate\Pagination\LengthAwarePaginator;

use App\Contracts\Movie\MovieRepositoryInterface;
use App\Models\Movie;
use App\Repositories\Eloquent\BaseRepository;

class MovieRepository extends BaseRepository implements MovieRepositoryInterface
{
    private const SORTABLE = ['title', 'rating', 'created_at'];
    /**
     * Create a new class instance.
     */
    public function __construct(Movie $movie)
    {
        parent::__construct($movie);
    }

    /**
     * Filter movies based on the given criteria.
     *
     * @param array $filters
     * @param string $sortBy
     * @param string $order
     * @return LengthAwarePaginator
     */
    public function filter(array $filters, string $sortBy = 'title', string $order = 'asc', int $perPage = 15): LengthAwarePaginator
    {
        $query = Movie::with('genres');
        $query->when($filters['search'] ?? null, function ($query, $search) {
            return $query->whereRaw('LOWER(title) LIKE ?', ['%' . strtolower($search) . '%']);
        });

        $query->when($filters['year'] ?? null, function ($query, $year){
            return $query->where('year', $year);
        });

        $query->when($filters['min_rating'] ?? null, function($query, $minRating){
            return $query->where('rating', '>=', $minRating);
        });

        $query->when($filters['genre_id'] ?? null, function($query, $genreId){
            return $query->whereHas('genres', function($subQuery) use ($genreId) {
                $subQuery->where('genres.id', $genreId);
            });
        });

        $sortColumn = in_array($sortBy, self::SORTABLE) ? $sortBy : 'title';
        $order = $order === 'desc' ? 'desc' : 'asc';

        return $query->orderBy($sortColumn, $order)->paginate($perPage);
    }

    /**
     * Sync the genres associated with the given movie.
     *
     * @param Movie $movie
     * @param array $genreIds
     * @return void
     */
    public function syncGenres(Movie $movie, array $genreIds): void
    {
        $movie->genres()->sync($genreIds);
    }
}
