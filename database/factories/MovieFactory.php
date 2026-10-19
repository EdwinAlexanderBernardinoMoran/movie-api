<?php

namespace Database\Factories;

use App\Models\Movie;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Movie>
 */
class MovieFactory extends Factory
{
    /**
     * The model that this factory corresponds to.
     */
    protected $model = Movie::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = $this->faker->unique()->sentence(3);

        return [
            'title' => $title,
            'slug' => Str::slug($title),
            'synopsis' => $this->faker->paragraph(),
            'year' => $this->faker->numberBetween(1980, 2024),
            'rating' => $this->faker->randomFloat(1, 0, 10),
            'director' => $this->faker->name(),
            'duration' => $this->faker->numberBetween(80, 180),
            'poster' => null,
            'is_active' => true,
        ];
    }
}
