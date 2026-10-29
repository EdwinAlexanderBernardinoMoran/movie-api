<?php

use App\Models\Movie;
use App\Services\AI\MovieAIService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

$context = 'Una pelicula epica sobre el destino de la humanidad';
$aiRecommendations = '/api/v1/ai/recommendations';

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

it('denies a viewer from generating a synopsis', function () {
    $movie = Movie::factory()->create();

    $this->actingAs(userRole('viewer'), 'api')
        ->postJson("/api/v1/movies/{$movie->id}/generate-synopsis")
        ->assertForbidden();
});

it('returns bad gateway when synopsis generation fails', function () {
    $mock = Mockery::mock(MovieAIService::class);
    $mock->shouldReceive('generateSynopsis')->andThrow(new RuntimeException('boom'));
    app()->instance(MovieAIService::class, $mock);

    $movie = Movie::factory()->create();

    $this->actingAs(userRole('editor'), 'api')
        ->postJson("/api/v1/movies/{$movie->id}/generate-synopsis")
        ->assertStatus(502);
});

it('returns recommendations for authenticated users', function () use($aiRecommendations) {
    $recommendations = [['title' => 'Inception', 'year' => 2010, 'reason' => 'Mente']];
    mockAIService('getRecommendations', $recommendations);

    $this->actingAs(userRole('viewer'), 'api')
        ->postJson($aiRecommendations, ['genres' => ['Accion', 'Ciencia ficcion']])
        ->assertOk()
        ->assertJsonPath('message', 'Recommendations generated successfully')
        ->assertJsonPath('data.0.title', 'Inception');
});

it('returns bad gateway when recommendations fail', function () use($aiRecommendations) {
    $mock = Mockery::mock(MovieAIService::class);
    $mock->shouldReceive('getRecommendations')->andThrow(new RuntimeException('boom'));
    app()->instance(MovieAIService::class, $mock);

    $this->actingAs(userRole('viewer'), 'api')
        ->postJson($aiRecommendations, ['genres' => ['Accion']])
        ->assertStatus(502);
});

it('rejects recommendations without authentication', function () use($aiRecommendations) {
    $this->postJson($aiRecommendations, ['genres' => ['Accion']])
        ->assertUnauthorized();
});

it('validates the recommendations genres', function (mixed $genres) use($aiRecommendations) {
    $this->actingAs(userRole('viewer'), 'api')
        ->postJson($aiRecommendations, ['genres' => $genres])
        ->assertUnprocessable()
        ->assertJsonValidationErrors('genres');
})->with([
    'missing' => [null],
    'empty' => [[]],
    'too many' => [['a', 'b', 'c', 'd', 'e', 'f']],
]);
