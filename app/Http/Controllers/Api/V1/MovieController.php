<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;

use App\Contracts\Movie\MovieRepositoryInterface;

use App\Models\Movie;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\Movie\MovieResource;

use App\Traits\ApiResponse;

class MovieController extends Controller
{
    use ApiResponse;

    public function __construct(private MovieRepositoryInterface $movieRepository)
    {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $movies = $this->movieRepository->filter(
            filters: $request->only(['search', 'year', 'min_rating', 'genre_id']),
            sortBy: $request->input('sort_by', 'title'),
            order: $request->input('order', 'asc')
        );

        return $this->successResponse(
            message: 'Movies retrieved successfully',
            data: MovieResource::collection($movies)
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Movie $movie)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Movie $movie)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Movie $movie)
    {
        //
    }
}
