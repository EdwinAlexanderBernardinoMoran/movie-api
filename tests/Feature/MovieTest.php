<?php
use App\Models\Genre;

use App\Models\Movie;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('return all genres', function () {
    Movie::factory()->count(5)->create();
    $response = $this->get('/api/movies');

    $response->assertStatus(200)->assertJsonCount(5, 'data');
});

it('return a single movie', function () {
    $movie = Movie::factory()->create();
    $response = $this->get("/api/movies/{$movie->id}");

    $response->assertStatus(200)->assertJsonPath('data.id', $movie->id);
});

it('creates a movie with auto slug on store', function () {
    $payload = [
        'title' => 'Inception',
        'synopsis' => 'A mind-bending thriller',
        'year' => 2010,
        'rating' => 8.8,
        'director' => 'Christopher Nolan',
        'duration' => 148,
        'poster' => null,
        'is_active' => true,
    ];

    $response = $this->postJson('/api/movies', $payload);

    $response->assertStatus(201)
        ->assertJsonPath('data.title', $payload['title'])
        ->assertJsonPath('data.slug', 'inception');

    $this->assertDatabaseHas('movies', [
        'title' => $payload['title'],
        'slug' => 'inception',
    ]);
});

it('updates a movie on put', function () {
    $movie = Movie::factory()->create();
    $payload = [
        'title' => 'Interstellar',
        'synopsis' => 'A journey through space and time',
        'year' => 2014,
        'rating' => 8.6,
        'director' => 'Christopher Nolan',
        'duration' => 169,
        'poster' => 'https://example.com/interstellar.jpg',
        'is_active' => true,
    ];

    $response = $this->putJson("/api/movies/{$movie->id}", $payload);

    $response->assertStatus(200)
        ->assertJsonPath('data.title', $payload['title'])
        ->assertJsonPath('data.slug', 'interstellar');

    $this->assertDatabaseHas('movies', [
        'id' => $movie->id,
        'title' => $payload['title'],
        'slug' => 'interstellar',
    ]);
});

it('deletes a movie on delete', function () {
    $movie = Movie::factory()->create();

    $response = $this->deleteJson("/api/movies/{$movie->id}");

    $response->assertStatus(204);

    $this->assertSoftDeleted('movies', [
        'id' => $movie->id,
    ]);
});

it('syncs genres when provided on store', function () {

    $genres = Genre::factory()->count(3)->create();

    $payload = [
        'title' => 'Inception',
        'synopsis' => 'A mind-bending thriller',
        'year' => 2010,
        'rating' => 8.8,
        'director' => 'Christopher Nolan',
        'duration' => 148,
        'poster' => null,
        'is_active' => true,
        'genre_ids' => $genres->pluck('id')->toArray(),
    ];

    $response = $this->postJson('/api/movies', $payload);
    $response->assertCreated();

    $movie = Movie::latest()->first();

    $this->assertCount(3, $movie->genres);
    $this->assertEquals($genres->pluck('id')->sort()->values(), $movie->genres->pluck('id')->sort()->values());
});

it('validates genre_ids exist on store', function () {

    $payload = [
        'title' => 'Inception',
        'synopsis' => 'A mind-bending thriller',
        'year' => 2010,
        'rating' => 8.8,
        'director' => 'Christopher Nolan',
        'duration' => 148,
        'poster' => null,
        'is_active' => true,
        'genre_ids' => [999], // Non-existent genre ID
    ];

    $response = $this->postJson('/api/movies', $payload);

    $response->assertUnprocessable()
        ->assertJsonPath('message', 'The selected genre_ids.0 is invalid.')
        ->assertJsonValidationErrors('genre_ids.0');
});
