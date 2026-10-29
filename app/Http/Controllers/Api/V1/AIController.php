<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\AI\RecommendationsRequest;
use App\Models\Movie;

use App\Services\AI\MovieAIService;

use App\Traits\ApiResponse;

class AIController extends Controller
{
    use ApiResponse;

    /**
     * Initialize the AIController with the MovieAIService.
     */
    public function __construct(private MovieAIService $movieAIService)
    {}

    /**
     * Generate a synopsis for the given movie.
     *
     * @param Movie $movie
     * @return JsonResponse
     */
    public function generateSynopsis(Movie $movie): JsonResponse
    {
        try {
            $synopsis = $this->movieAIService->generateSynopsis($movie->load('genres'));

        } catch (\Throwable $th) {
            return $this->errorResponse(
                message: 'Error generating synopsis' . $th->getMessage(),
                statusCode: Response::HTTP_BAD_GATEWAY
            );
        }

        return $this->successResponse(
            message: 'Synopsis generated successfully',
            data: ['title' => $movie->title, 'synopsis' => $synopsis],
            statusCode: Response::HTTP_OK
        );
    }

    /**
     * Generate movie recommendations based on the given movie.
     *
     * @param RecommendationsRequest $request
     * @return JsonResponse
     */
    public function recommendations(RecommendationsRequest $request): JsonResponse
    {
        try {
            $recommendations = $this->movieAIService->getRecommendations($request->validated('genres'));

        } catch (\Throwable $th) {
            return $this->errorResponse(
                message: 'Error generating recommendations' . $th->getMessage(),
                statusCode: Response::HTTP_BAD_GATEWAY
            );
        }

        return $this->successResponse(
            message: 'Recommendations generated successfully',
            data: $recommendations,
            statusCode: Response::HTTP_OK
        );
    }
}
