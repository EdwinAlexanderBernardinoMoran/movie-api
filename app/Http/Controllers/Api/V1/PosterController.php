<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Movie\StorePosterRequest;
use App\Http\Resources\V1\Movie\MovieResource;

use App\Models\Movie;

use App\Traits\ApiResponse;

class PosterController extends Controller
{
    use ApiResponse;

    /**
     * Upload a poster for the specified movie.
     *
     * @param StorePosterRequest $request
     * @param Movie $movie
     * @return JsonResponse
     */
    public function store(StorePosterRequest $request, Movie $movie): JsonResponse
    {
        if ($movie->poster && Storage::disk('public')->exists($movie->poster)) {
            Storage::disk('public')->delete($movie->poster);
        }

        $path = $request->file('poster')->store('posters', 'public');

        $movie->poster = $path;
        $movie->save();

        return $this->successResponse(
            message: 'Poster uploaded successfully.',
            data: new MovieResource($movie->load('genres'))
        );
    }
}
