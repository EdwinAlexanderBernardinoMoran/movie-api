<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

const TEST_EMAIL = 'test@example.com';
const TEST_PASSWORD = 'password@2026';
const REGISTER_ENDPOINT = '/api/auth/register';

test('create user and returns token on register', function () {
    $payload = [
        'name' => 'Test User',
        'email' => TEST_EMAIL,
        'password' => TEST_PASSWORD,
        'password_confirmation' => TEST_PASSWORD
    ];

    $response = $this->post(REGISTER_ENDPOINT, $payload);

    $response->assertStatus(201)
        ->assertJsonStructure([
            'data' => [
                'access_token',
                'token_type',
                'expires_in',
                'user'
            ]
        ])
        ->assertJsonPath('data.user.email', TEST_EMAIL);
});

test('does not expose password on register', function () {
    $payload = [
        'name' => 'Test User',
        'email' => TEST_EMAIL,
        'password' => TEST_PASSWORD,
        'password_confirmation' => TEST_PASSWORD
    ];

    $response = $this->post(REGISTER_ENDPOINT, $payload);

    expect($response->json('data.user'))->not->toHaveKey('password');
});

test('requires payload on register', function () {
    $response = $this->post(REGISTER_ENDPOINT, []);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['name', 'email', 'password']);
});

test('returns token for valid credentials on login', function () {

    User::factory()->create([
        'email' => TEST_EMAIL,
        'password' => bcrypt(TEST_PASSWORD)
    ]);

    $response = $this->post('/api/auth/login', [
        'email' => TEST_EMAIL,
        'password' => TEST_PASSWORD
    ]);

    $response->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                'access_token',
                'token_type',
                'expires_in',
                'user'
            ]
        ]);
});

test('returns user with valid token on me', function () {
    $user = User::factory()->create([
        'email' => TEST_EMAIL,
        'password' => bcrypt(TEST_PASSWORD)
    ]);

    $this->actingAs($user, 'api')
        ->get('/api/auth/me')
        ->assertStatus(200)
        ->assertJsonPath('data.email', TEST_EMAIL);
});

test('succeeds with valid token on logout', function () {
    $user = User::factory()->create([
        'email' => TEST_EMAIL,
        'password' => bcrypt(TEST_PASSWORD)
    ]);

    $this->actingAs($user, 'api')
        ->postJson('/api/auth/logout')
        ->assertStatus(200);
});
