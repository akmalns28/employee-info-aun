<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(web: __DIR__ . '/../routes/web.php', commands: __DIR__ . '/../routes/console.php', health: '/up')
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'profile.complete' => \App\Http\Middleware\CheckProfileComplete::class,
        ]);
    })
    ->withExceptions(function ( ) {
        // $exceptions->render(function (Throwable $e, Request $request) {
        //     if ($e instanceof HttpExceptionInterface) {
        //         $status = $e->getStatusCode();

        //         if (view()->exists("errors.$status")) {
        //             return response()->view("errors.$status", [], $status);
        //         }
        //     }

        //     return response()->view('errors.500', [], 500);
        // });
    })
    ->create();
