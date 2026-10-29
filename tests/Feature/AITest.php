<?php

use App\Models\Movie;
use App\Services\AI\MovieAIService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

$context = 'Una pelicula epica sobre el destino de la humanidad';

function mockAIService(string $method, mixed $returnValue)
{
    $mock = Mockery::mock(MovieAIService::class);
    $mock->shouldReceive($method)->andReturn($returnValue);
    app()->instance(MovieAIService::class, $mock);
}

it('allows an editor to generate a synopsis', function () use ($context) {
    mockAIService('generateSynopsis', $context);

    $movie = Movie::factory()->create();

    $this->actingAs(userRole('editor'), 'api')
         ->postJson("/api/v1/movies/{$movie->id}/generate-synopsis")
         ->assertOk()
         ->assertJsonPath('message', 'Synopsis generated successfully')
         ->assertJsonPath('data.synopsis', 'Una pelicula epica sobre el destino de la humanidad')
         ->assertJsonPath('data.title', $movie->title);

});

it('allows an admin to generate a synopsis', function () use ($context) {
    mockAIService('generateSynopsis', $context);

    $movie = Movie::factory()->create();

    $this->actingAs(userRole('admin'), 'api')
         ->postJson("/api/v1/movies/{$movie->id}/generate-synopsis")
         ->assertOk();
});
