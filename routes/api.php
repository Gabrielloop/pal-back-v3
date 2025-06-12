<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\UserController;

// Routes publiques
Route::post('/login', [AuthController::class, 'login']);
Route::post('/users', [UserController::class, 'store']);

// Routes pour utilisateurs authentifiés : token
Route::middleware('auth:sanctum')->group(function () {

    // Déconnexion
    Route::post('/logout', [AuthController::class, 'logout']);

    // Gestion des utilisateurs (accessibles à tous les utilisateurs connectés)
    Route::get('/me', [UserController::class, 'me']);
    Route::put('/me', [UserController::class, 'updateMe']);

    // Routes réservées aux admins : token + role admin
    Route::middleware(['auth:sanctum', 'is_admin'])->group(function () {
        Route::get('/users', [UserController::class, 'index']);
        Route::get('/users/{id}', [UserController::class, 'show']);
        Route::put('/users/{id}', [UserController::class, 'update']);
        Route::delete('/users/{id}', [UserController::class, 'destroy']);
    });
});
