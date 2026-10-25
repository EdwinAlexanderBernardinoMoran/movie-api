<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

use App\Contracts\Movie\MovieRepositoryInterface;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Movie\StoreMovieRequest;
use App\Http\Requests\V1\Movie\UpdateMovieRequest;
use App\Http\Resources\V1\Movie\MovieResource;

use App\Models\Movie;
use App\Traits\ApiResponse;

class MovieController extends Controller
{
    use ApiResponse;

    public function __construct(private MovieRepositoryInterface $movieRepository) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $movies = $this->movieRepository->filter(
            filters: $request->only(['search', 'year', 'min_rating', 'genre_id']),
            sortBy: $request->input('sort_by', 'title'),
            order: $request->input('order', 'asc'),
            perPage: max(1, min(50, $request->input('perPage', 15)))
        );

        return MovieResource::collection($movies);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMovieRequest $request)
    {
        $movie = $this->movieRepository->create($request->safe()->except('genre_ids'));

        if ($request->has('genre_ids')) {
            $this->movieRepository->syncGenres($movie, $request->input('genre_ids'));
        }

        $movie->load('genres');

        return $this->successResponse(
            message: 'Movie created successfully',
            data: new MovieResource($movie),
            statusCode: Response::HTTP_CREATED
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(string $movie)
    {
        $movie = $this->movieRepository->findOrFail($movie);
        $movie->load('genres');

        return $this->successResponse(
            message: 'Movie retrieved successfully',
            data: new MovieResource($movie)
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMovieRequest $request, Movie $movie)
    {
        $movie = $this->movieRepository->update($movie, $request->safe()->except('genre_ids'));

        if ($request->has('genre_ids')) {
            $this->movieRepository->syncGenres($movie, $request->input('genre_ids'));
        }

        $movie->load('genres');

        return $this->successResponse(
            message: 'Movie updated successfully',
            data: new MovieResource($movie)
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Movie $movie)
    {
        $this->movieRepository->delete($movie);

        return response()->noContent();
    }
}
