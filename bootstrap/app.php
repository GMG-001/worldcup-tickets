<?php

use App\Exceptions\InsufficientSeatsException;
use App\Http\Middleware\IsAdmin;
use App\Http\Middleware\IsFan;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\JsonResponse;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'is-admin' => IsAdmin::class,
            'is-fan'   => IsFan::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (InsufficientSeatsException $e): JsonResponse {
            return response()->json(['message' => $e->getMessage()], 422);
        });
    })->create();
