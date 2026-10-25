<?php

namespace App\Http\Resources\V1\Movie;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Request;

use App\Http\Resources\V1\Genre\GenreResource;

class MovieResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'synopsis' => $this->synopsis,
            'year' => $this->year,
            'rating' => $this->rating,
            'director' => $this->director,
            'duration' => $this->duration,
            'poster_url' => $this->poster ? asset('storage/' . $this->poster) : null,
            'is_active' => $this->is_active,
            'genres' => GenreResource::collection($this->whenLoaded('genres')),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
