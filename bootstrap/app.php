<?php

use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use App\Http\Middleware\CheckRole;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        apiPrefix: 'api',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->redirectGuestsTo(fn () => null);
        $middleware->alias(['role' => CheckRole::class]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );

        /**
         * 404
         */
        $exceptions->render(function(ModelNotFoundException | NotFoundHttpException $e, Request $request) {
            if($request->is('api/*')) {
                return response()->json(['message' => 'Resource not found'], Response::HTTP_NOT_FOUND);
            }
        });

        /**
         * 401
         */
        $exceptions->render(function(AuthenticationException $e, Request $request) {
            if($request->is('api/*')) {
                return response()->json(['message' => 'No Authenticate'], Response::HTTP_UNAUTHORIZED);
            }
        });

        /**
         * 500
         */
        $exceptions->renderable(function (\Throwable $e, Request $request){
            if ($request->is('api/*') && !config('app.debug')) {
                return response()->json(['message' => 'Internal Error Server'], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        });
    })->create();
