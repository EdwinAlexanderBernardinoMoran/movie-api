<?php

test('return a successful response', function () {
    $response = $this->get('/api/v1/health');

    $response->assertStatus(200);
    $response->assertJson([
        'status' => true,
        'message' => 'API is healthy',
        'data' => [
            'items' => 200,
        ],
    ]);
});
