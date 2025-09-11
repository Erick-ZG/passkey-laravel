<?php

use App\Http\Middleware\HandleAppearance;
use App\Http\Middleware\HandleInertiaRequests;
use App\Http\Middleware\EnsureAccountOwner; // 👈 importa tu middleware
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets;
// (opcional) si quieres alias para WorkOS:
// use Laravel\WorkOS\Http\Middleware\ValidateSessionWithWorkOS;

return Application::configure(basePath: dirname(__DIR__)) // 👈 asegúrate de usar __DIR__
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->encryptCookies(except: ['appearance', 'sidebar_state']);

        // 🔑 Aliases de route middleware (se usan en las rutas)
        $middleware->alias([
            'account.owner' => EnsureAccountOwner::class,
            // 'workos.session' => ValidateSessionWithWorkOS::class, // opcional
        ]);

        $middleware->web(append: [
            HandleAppearance::class,
            HandleInertiaRequests::class,
            AddLinkHeadersForPreloadedAssets::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
