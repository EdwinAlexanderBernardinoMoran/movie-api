<?php

namespace App\Traits;

trait ApiResponse
{
    /**
     * Return a success response.
     */
    protected function successResponse(mixed $data, string $message, int $statusCode = 200): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'status' => true,
            'message' => $message,
            'data' => $data,
        ], $statusCode);
    }

    /**
     * Return an error response.
     */
    protected function errorResponse(string $message, int $statusCode, mixed $errors): \Illuminate\Http\JsonResponse
    {
        $payload = ['message' => $message];

        if (!empty($errors)) {
            $payload['errors'] = $errors;
        }

        return response()->json($payload, $statusCode);
    }
}
