<?php

test('return a successful response', function () {
    $response = $this->get('/api/health');

    $response->assertStatus(200);
    $response->assertJson([
        'status' => true,
        'message' => 'API is healthy',
        'data' => [
            'items' => 200,
        ],
    ]);
});
