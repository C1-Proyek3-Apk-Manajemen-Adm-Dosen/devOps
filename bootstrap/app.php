<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Registrasi middleware global & alias
        $middleware->alias([
            'checkRole' => \App\Http\Middleware\CheckRole::class, // ✅ ini middleware kamu
        ]);

        // Kalau mau dijadikan middleware grup juga (optional)
        $middleware->web(append: [
            // middleware yang aktif di semua route web bisa ditaruh sini
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
