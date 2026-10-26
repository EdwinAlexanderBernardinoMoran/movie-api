<?php

use App\Models\Movie;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('Includes meta and links on movies index', function () {
    Movie::factory(5)->create();

    $this->getJson('/api/v1/movies')
        ->assertOk()
        ->assertJsonStructure([
            'data',
            'links' => [
                'first',
                'last',
                'prev',
                'next'
            ],
            'meta' => [
                'current_page',
                'from',
                'last_page',
                'path',
                'per_page',
                'to',
                'total'
            ]
        ]);
});

test('Allows configurable per_page on movies index', function () {
    Movie::factory(10)->create();

    $this->getJson('/api/v1/movies?perPage=4')
        ->assertOk()
        ->assertJsonPath('meta.per_page', 4)
        ->assertJsonCount(4, 'data');
});
