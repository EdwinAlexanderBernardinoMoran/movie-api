<?php

use Anthropic\Client;
use App\Models\Genre;
use App\Models\Movie;
use App\Services\AI\MovieAIService;
use GuzzleHttp\Psr7\Response;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

uses(RefreshDatabase::class);

function makeAIService(string $text, ?Closure $assertBody = null): MovieAIService
{
    config(['services.anthropic.api_key' => 'test-key', 'services.anthropic.model' => 'test-model']);

    $transporter = new class($text, $assertBody) implements ClientInterface
    {
        public function __construct(private string $text, private ?Closure $assertBody) {}

        public function sendRequest(RequestInterface $request): ResponseInterface
        {
            if ($this->assertBody) {
                ($this->assertBody)(json_decode((string) $request->getBody(), true));
            }

            return new Response(200, ['Content-Type' => 'application/json'], json_encode([
                'id' => 'msg_test',
                'type' => 'message',
                'role' => 'assistant',
                'model' => 'test-model',
                'content' => [['type' => 'text', 'text' => $this->text]],
                'stop_reason' => 'end_turn',
                'stop_sequence' => null,
                'usage' => ['input_tokens' => 1, 'output_tokens' => 1],
            ]));
        }
    };

    $service = new MovieAIService;

    $property = new ReflectionProperty($service, 'client');
    $property->setValue($service, new Client(apiKey: 'test-key', requestOptions: ['transporter' => $transporter]));

    return $service;
}

it('generates a trimmed synopsis including movie details in the prompt', function () {
    $movie = Movie::factory()->create(['title' => 'Matrix', 'year' => 1999]);
    $movie->genres()->attach(Genre::factory()->create(['name' => 'Ciencia ficcion']));

    $service = makeAIService("  Una sinopsis.  \n", function (array $body) {
        expect($body['model'])->toBe('test-model')
            ->and($body['max_tokens'])->toBe(300)
            ->and($body['messages'][0]['content'])->toContain('Matrix', '1999', 'Ciencia ficcion');
    });

    expect($service->generateSynopsis($movie->load('genres')))->toBe('Una sinopsis.');
});

it('parses a pure JSON recommendations response', function () {
    $json = json_encode([['title' => 'Inception', 'year' => 2010, 'reason' => 'Sueños']]);

    $service = makeAIService($json, function (array $body) {
        expect($body['messages'][0]['content'])->toContain('Accion, Drama');
    });

    expect($service->getRecommendations(['Accion', 'Drama']))
        ->toBe([['title' => 'Inception', 'year' => 2010, 'reason' => 'Sueños']]);
});

it('extracts the JSON array from a response wrapped in text', function () {
    $raw = 'Aqui van: [{"title":"Heat","year":1995,"reason":"Accion"}] disfruta';

    expect(makeAIService($raw)->getRecommendations(['Accion']))
        ->toBe([['title' => 'Heat', 'year' => 1995, 'reason' => 'Accion']]);
});

it('returns an empty array when the response is not valid recommendations', function (string $raw) {
    expect(makeAIService($raw)->getRecommendations(['Accion']))->toBe([]);
})->with([
    'plain text' => ['no hay json'],
    'missing keys' => ['[{"title":"Heat"}]'],
    'empty array' => ['[]'],
]);

it('validates recommendation arrays', function () {
    config(['services.anthropic.api_key' => 'test-key']);
    $service = new MovieAIService;

    expect($service->isValidRecommendationArray([['title' => 'A', 'reason' => 'B']]))->toBeTrue()
        ->and($service->isValidRecommendationArray([['title' => 'A']]))->toBeFalse()
        ->and($service->isValidRecommendationArray([]))->toBeFalse();
});
