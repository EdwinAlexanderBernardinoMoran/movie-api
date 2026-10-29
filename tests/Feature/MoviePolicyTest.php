<?php

use App\Models\User;
use App\Policies\MoviePolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('authorizes movie actions by role', function (string $role, string $ability, bool $expected) {
    $user = User::factory()->create(['role' => $role]);

    expect((new MoviePolicy)->{$ability}($user))->toBe($expected);
})->with(function () {
    foreach (['create', 'update'] as $ability) {
        yield "admin {$ability}" => ['admin', $ability, true];
        yield "editor {$ability}" => ['editor', $ability, true];
        yield "viewer {$ability}" => ['viewer', $ability, false];
    }
    foreach (['delete', 'restore', 'forceDelete'] as $ability) {
        yield "admin {$ability}" => ['admin', $ability, true];
        yield "editor {$ability}" => ['editor', $ability, false];
        yield "viewer {$ability}" => ['viewer', $ability, false];
    }
});

it('allows anyone to view movies', function () {
    $policy = new MoviePolicy;

    expect($policy->viewAny())->toBeTrue()->and($policy->view())->toBeTrue();
});
