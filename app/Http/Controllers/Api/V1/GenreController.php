<?php

namespace App\Http\Controllers\Api\V1;

use Symfony\Component\HttpFoundation\Response;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Genre\StoreGenreRequest;
use App\Http\Requests\V1\Genre\UpdateGenreRequest;
use App\Http\Resources\V1\Genre\GenreResource;

use App\Models\Genre;

use App\Traits\ApiResponse;

class GenreController extends Controller
{
    use ApiResponse;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $genres = Genre::all();

        return $this->successResponse(
            message: 'Genres retrieved successfully',
            data: GenreResource::collection($genres)
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreGenreRequest $request)
    {
        $genre = Genre::create($request->validated());

        return $this->successResponse(
            message: 'Genre created successfully',
            data: new GenreResource($genre),
            statusCode: Response::HTTP_CREATED
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Genre $genre)
    {
        return $this->successResponse(
            message: 'Genre retrieved successfully',
            data: new GenreResource($genre)
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateGenreRequest $request, Genre $genre)
    {
        $genre->update($request->validated());

        return $this->successResponse(
            message: 'Genre updated successfully',
            data: new GenreResource($genre)
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Genre $genre)
    {
        $genre->delete();

        return $this->successResponse(
            message: 'Genre deleted successfully',
            data: null,
            statusCode: Response::HTTP_NO_CONTENT
        );
    }
}
