<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            \App\Http\Middleware\HandleInertiaRequests::class,
            \Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets::class,
        ]);

        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );

        $exceptions->render(function (\Throwable $e, Request $request) {
            // Capture specific exceptions for Inertia requests to provide a friendly UI message
            if ($request->hasHeader('X-Inertia')) {
                if ($e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException) {
                    return back()->with('error', 'El recurso solicitado no fue encontrado.');
                }
                if ($e instanceof \Illuminate\Database\QueryException) {
                    // Log the error or handle it
                    return back()->with('error', 'Error de base de datos al procesar la solicitud.');
                }
            }

            return null; // Bubbles up to default Laravel handler
        });
    })->create();

