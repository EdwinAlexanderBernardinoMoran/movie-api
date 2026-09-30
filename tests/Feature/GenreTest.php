<?php

use App\Models\Genre;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('return all genres', function () {
    Genre::factory()->count(5)->create();
    $response = $this->get('/api/genres');

    $response->assertStatus(200)->assertJsonCount(5, 'data');
});

it('return a single genre', function () {
    $genre = Genre::factory()->create();
    $response = $this->get("/api/genres/{$genre->id}");

    $response->assertStatus(200)->assertJsonPath('data.id', $genre->id);
});

it('return a single genre on show', function () {
    $genre = Genre::factory()->create();
    $response = $this->get("/api/genres/{$genre->id}");

    $response->assertStatus(200)->assertJsonPath('data.id', $genre->id);
});

it('creates a genre with auto slug on store', function () {
    $payload = ['name' => 'Comedia', 'description' => 'Gênero de comédia'];

    $response = $this->postJson('/api/genres', $payload);

    $response->assertStatus(201)
        ->assertJsonPath('data.name', $payload['name'])
        ->assertJsonPath('data.slug', 'comedia');

    $this->assertDatabaseHas('genres', [
        'name' => $payload['name'],
        'slug' => 'comedia',
    ]);
});

it('updates a genre on put', function () {
    $genre = Genre::factory()->create();
    $payload = ['name' => 'Terror', 'description' => 'Gênero de terror'];

    $response = $this->putJson("/api/genres/{$genre->id}", $payload);

    $response->assertStatus(200)
        ->assertJsonPath('data.name', $payload['name'])
        ->assertJsonPath('data.slug', 'terror');

    $this->assertDatabaseHas('genres', [
        'id' => $genre->id,
        'name' => $payload['name'],
        'slug' => 'terror',
    ]);
});

it('deletes a genre on delete', function () {
    $genre = Genre::factory()->create();

    $response = $this->deleteJson("/api/genres/{$genre->id}");

    $response->assertStatus(204);

    $this->assertSoftDeleted('genres', [
        'id' => $genre->id,
    ]);
});
