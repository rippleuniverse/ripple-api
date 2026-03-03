<?php

use App\Enums\StatusCode;
use Fruitcake\Cors\Exceptions\InvalidOptionException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
        apiPrefix: '/',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (AuthenticationException $e, $request) {
            return response()->json([
                'message' => 'Unauthenticated.'
            ], StatusCode::Unauthorized->value);
        });

        $exceptions->render(function (InvalidOptionException $e, $request) {
            return response()->json([
                'message' => $e->getMessage()
            ], StatusCode::InternalServerError->value);
        });

    })->create();
