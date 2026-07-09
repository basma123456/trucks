<?php

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
    web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {

    $middleware->alias([
        'turn_auth' => \App\Http\Middleware\TurnAuth::class,
    ]);
})
//    ->withExceptions(function (Exceptions $exceptions): void {
//        //
//    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (AuthenticationException $e, Request $request) {

            if (in_array('visitor', $e->guards())) {
                return response()->json([
                    'success' => false,
                    'message' => 'Visitor is not authenticated.',
                    'code' => 401,
                ], 401);
            }

            return null;
        });
    })
    ->create();
