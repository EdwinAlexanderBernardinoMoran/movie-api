<?php

use App\Models\Genre;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('filters genres by search text', function () {
    Genre::factory()->create(['name' => 'Action']);
    Genre::factory()->create(['name' => 'Comedy']);

    $response = $this->get('/api/genres?search=act');

    $response->assertStatus(200)
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.name', 'Action');
});

it('filters genres by is_active status', function () {
    Genre::factory()->create(['name' => 'Action', 'is_active' => true]);
    Genre::factory()->create(['name' => 'Comedy', 'is_active' => false]);

    $response = $this->get('/api/genres?is_active=false');

    $response->assertStatus(200)
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.name', 'Comedy');
});

it('orders genres by name in descending order', function () {
    Genre::factory()->create(['name' => 'Action']);
    Genre::factory()->create(['name' => 'Comedy']);

    $response = $this->get('/api/genres?sort_by=name&order=desc');
    $names = collect($response->json('data'))->pluck('name')->values()->all();

    expect($names)->toEqual(['Comedy', 'Action']);

    $response->assertStatus(200)
        ->assertJsonCount(2, 'data');
});

it('ignores an invalid sort column', function () {
    Genre::factory()->create(['name' => 'Action']);
    Genre::factory()->create(['name' => 'Comedy']);

    $response = $this->get('/api/genres?sort_by=password');
    $names = collect($response->json('data'))->pluck('name')->values()->all();

    expect($names)->toEqual(['Action', 'Comedy']);

    $response->assertStatus(200)
        ->assertJsonCount(2, 'data');
});

it('returns the correct genre by slug', function () {
    Genre::factory()->create(['name' => 'Action', 'slug' => 'action']);

    $response = $this->getJson("/api/genres/slug/action");

    $response->assertStatus(200)
        ->assertJsonPath('data.slug', 'action');
});

it('restores a soft-deleted genre', function () {
    $genre = Genre::factory()->create(['name' => 'Action']);
    $genre->delete();

    $this->assertSoftDeleted($genre, ['id' => $genre->id]);

    $response = $this->postJson("/api/genres/restore/{$genre->id}");

    $response->assertStatus(200)
        ->assertJsonPath('data.name', 'Action');
});
