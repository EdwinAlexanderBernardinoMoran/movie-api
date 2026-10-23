<?php

namespace App\Repositories\Movie;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

use App\Contracts\Movie\MovieRepositoryInterface;
use App\Models\Genre;
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
     * @return Collection
     */
    public function filter(array $filters, string $sortBy = 'title', string $order = 'asc'): Collection
    {
        $key = 'movies.index.' . hash('sha256', serialize(compact('filters', 'sortBy', 'order')));

        $moviesJson = Cache::remember($key, now()->addMinutes(60), function () use ($filters, $sortBy, $order) {
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

            return $query->orderBy($sortColumn, $order)->get()->toJson();
        });

        $data = json_decode($moviesJson, true);
        $movies = Movie::hydrate($data);
        $movies->each(function (Movie $movie, int $key) use ($data) {
            unset($movie->genres);
            $movie->setRelation('genres', Genre::hydrate($data[$key]['genres'] ?? []));
        });

        return $movies;
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
