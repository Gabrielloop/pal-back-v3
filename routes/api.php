<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\BookController;

// Routes publiques
Route::post('/user/login', [AuthController::class, 'login']);
Route::post('/user/users', [UserController::class, 'store']);

// Routes pour utilisateurs authentifiés : token
Route::middleware('auth:sanctum')->group(function () {

    // Déconnexion
    Route::post('/users/logout', [AuthController::class, 'logout']);

    // Gestion des utilisateurs (accessibles à tous les utilisateurs connectés)
    Route::get('/users/me', [UserController::class, 'me']);
    Route::put('/users/me', [UserController::class, 'updateMe']);

    // Gestion des livres (accessibles à tous les utilisateurs connectés)
    Route::get('/books/isbn/{isbn}', [BookController::class, 'getBookByIsbn']);
    Route::get('/books/title/{title}', [BookController::class, 'getBooksByTitle']);
    Route::post('/books/add', [BookController::class, 'saveBook']);
    Route::put('/books/update/{isbn}', [BookController::class, 'updateBook']);
    Route::delete('/books/delete/{isbn}', [BookController::class, 'deleteBook']);

    // Routes réservées aux admins : token + role admin
    Route::middleware(['auth:sanctum', 'is_admin'])->group(function () {
        // Gestion des utilisateurs (accessibles uniquement aux admins)
        Route::get('/users', [UserController::class, 'index']);
        Route::get('/users/{id}', [UserController::class, 'show']);
        Route::put('/users/{id}', [UserController::class, 'update']);
        Route::delete('users/{id}', [UserController::class, 'destroy']);
        // Gestion des livres (accessibles uniquement aux admins)
        Route::get('/books/all', [BookController::class, 'getAllBooks']);
    });
});
