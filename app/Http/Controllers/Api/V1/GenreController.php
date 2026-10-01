<?php

namespace App\Http\Controllers\Api\V1;

use App\Contracts\Genre\GenreRepositoryInterface;
use Symfony\Component\HttpFoundation\Response;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Genre\StoreGenreRequest;
use App\Http\Requests\V1\Genre\UpdateGenreRequest;
use App\Http\Resources\V1\Genre\GenreResource;

use App\Models\Genre;

use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GenreController extends Controller
{
    use ApiResponse;

    /**
     * GenreController constructor.
     */
    public function __construct(private GenreRepositoryInterface $genreRepository)
    {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $genres = $this->genreRepository->filter(
            filters: $request->only(['search', 'is_active']),
            sortBy: $request->query('sortBy', 'name'),
            order: $request->query('order', 'asc')
        );

        return $this->successResponse(
            message: 'Genres retrieved successfully',
            data: GenreResource::collection($genres)
        );
    }

    /**
     * Display the specified resource by its slug.
     *
     * @param string $slug
     * @return JsonResponse
     */
    public function showBySlug(string $slug): JsonResponse
    {
        $genre = $this->genreRepository->findBySlugOrFail($slug);

        return $this->successResponse(
            message: 'Genre retrieved successfully',
            data: new GenreResource($genre)
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreGenreRequest $request)
    {
        $genre = $this->genreRepository->create($request->validated());

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
        $genre = $this->genreRepository->update($genre, $request->validated());

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
        $this->genreRepository->delete($genre);

        return $this->successResponse(
            message: 'Genre deleted successfully',
            data: null,
            statusCode: Response::HTTP_NO_CONTENT
        );
    }

    /**
     * Restore a soft-deleted genre by its ID.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function restore(int $id): JsonResponse
    {
        $genre = $this->genreRepository->restore($id);

        return $this->successResponse(
            message: 'Genre restored successfully',
            data: new GenreResource($genre)
        );
    }
}
