<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use App\Http\Middleware\LogUserCrud;
use App\Http\Middleware\ForceJsonResponse;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Middlewares globaux (toutes les requêtes)
        $middleware->append(LogUserCrud::class);

        // Alias personnalisés
        $middleware->alias([
            'log.crud' => LogUserCrud::class,
        ]);

        // Groupe API
        $middleware->group('api', [
            ForceJsonResponse::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // 404 : modèle ou route introuvable
        $exceptions->render(function (ModelNotFoundException|NotFoundHttpException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ressource introuvable.',
                ], 404);
            }
        });

        // 422 : erreur de validation
        $exceptions->render(function (ValidationException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur de validation.',
                    'errors' => $e->errors(),
                ], 422);
            }
        });

        // 401 : non authentifié
        $exceptions->render(function (AuthenticationException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Non authentifié.',
                ], 401);
            }
        });

        // 500 : erreur interne
        $exceptions->render(function (Throwable $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur interne.',
                    'error' => config('app.debug') ? $e->getMessage() : null,
                ], 500);
            }
        });
    })
    ->create();
