<?php

namespace App\Http\Middleware;

use App\Traits\ApiResponse;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    use ApiResponse;

    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (! in_array($request->user()->role, $roles)) {
            return $this->errorResponse(message: 'Unauthorized', statusCode: Response::HTTP_FORBIDDEN);
        }

        return $next($request);
    }
}
