<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

use App\Http\Controllers\Controller;

use App\Http\Requests\V1\User\RegisterRequest;

use App\Http\Resources\V1\User\UserResource;

use App\Models\User;

use App\Traits\ApiResponse;

use PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException;

class AuthController extends Controller
{
    use ApiResponse;

    /**
     * Register a new user and return an access token.
     *
     * @param RegisterRequest $request
     * @return JsonResponse
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        try {
            $user = User::create($request->validated());

            $token = Auth::guard('api')->login($user);

            if (!$token) {
                return $this->errorResponse(
                    message: 'User registration failed',
                    statusCode: Response::HTTP_INTERNAL_SERVER_ERROR
                );
            }

            return $this->successResponse(
                message: 'User registered successfully',
                data: $this->tokenPayload($user, $token),
                statusCode: Response::HTTP_CREATED
            );
        } catch (JWTException $e) {
            return $this->errorResponse(
                message: 'User registration failed',
                statusCode: Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Get the token payload for the authenticated user.
     *
     * @param User $user
     * @param string $token
     * @return array
     */
    public function tokenPayload(User $user, string $token): array
    {
        return [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => Auth::guard('api')->factory()->getTTL() * 60,
            'user' => new UserResource($user),
        ];
    }

    /**
     * Log in an existing user and return an access token.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        if (!$token = Auth::guard('api')->attempt($credentials)) {
            return $this->errorResponse(
                message: 'Invalid credentials',
                statusCode: Response::HTTP_UNAUTHORIZED
            );
        }

        return $this->successResponse(
            message: 'User logged in successfully',
            data: $this->tokenPayload(Auth::guard('api')->user(), $token),
            statusCode: Response::HTTP_OK
        );
    }

    /**
     * Get the authenticated user's details.
     *
     * @return JsonResponse
     */
    public function me(): JsonResponse
    {
        return $this->successResponse(
            message: 'Authenticated user retrieved successfully',
            data: new UserResource(Auth::guard('api')->user()),
            statusCode: Response::HTTP_OK
        );
    }

    /**
     * Log out the authenticated user and invalidate the access token.
     *
     * @return JsonResponse
     */
    public function logout(): JsonResponse
    {
        Auth::guard('api')->logout();

        return $this->successResponse(
            message: 'User logged out successfully',
            data: null,
            statusCode: Response::HTTP_OK
        );
    }

    /**
     * Refresh the access token for the authenticated user.
     *
     * @return JsonResponse
     */
    public function refresh(): JsonResponse
    {
        $token = Auth::guard('api')->refresh();

        return $this->successResponse(
            message: 'Token refreshed successfully',
            data: $this->tokenPayload(Auth::guard('api')->setToken($token)->user(), $token),
            statusCode: Response::HTTP_OK
        );
    }
}
