<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

use App\Http\Middleware\LogUserCrud;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // middleware de log global pour toutes les requêtes
        $middleware->append(LogUserCrud::class);
        // version alias
        $middleware->alias([
            'log.crud' => LogUserCrud::class,
        ]);

        // $middleware->web([
        //     // \App\Http\Middleware\EncryptCookies::class,
        //     // \Illuminate\Session\Middleware\StartSession::class,
        //     // \Illuminate\View\Middleware\ShareErrorsFromSession::class,
        //     // \App\Http\Middleware\VerifyCsrfToken::class,
        //     // \Illuminate\Routing\Middleware\SubstituteBindings::class,
        // ]);
        // $middleware->api([
        //     // \Illuminate\Routing\Middleware\SubstituteBindings::class,
        // ]);
        // $middleware->groupe('web', [...]);
        // $middleware-> alias('log.crud', LogUserCrud::class);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
