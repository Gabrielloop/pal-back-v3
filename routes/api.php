<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\BookController;
use App\Http\Controllers\API\CommentController;

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
    Route::post('/books', [BookController::class, 'saveBook']);
    Route::put('/books/{isbn}', [BookController::class, 'updateBook']);
    Route::delete('/books/{isbn}', [BookController::class, 'deleteBook']);

    // Gestion des commentaires (accessibles à tous les utilisateurs connectés)
    Route::get('/comments/{isbn}', [CommentController::class, 'getByIsbnForCurrentUser']);
    Route::post('/comments/{isbn}', [CommentController::class, 'store']);
    Route::put('/comments/{isbn}', [CommentController::class, 'update']);
    Route::delete('/comments/{isbn}', [CommentController::class, 'destroy']);


    // Routes réservées aux admins : token + role admin
    Route::middleware(['auth:sanctum', 'is_admin'])->group(function () {

        // Gestion des utilisateurs (accessibles uniquement aux admins)
        Route::get('/users', [UserController::class, 'index']);
        Route::get('/users/{id}', [UserController::class, 'show']);
        Route::put('/users/{id}', [UserController::class, 'update']);
        Route::delete('users/{id}', [UserController::class, 'destroy']);

        // Gestion des livres (accessibles uniquement aux admins)
        Route::get('/books', [BookController::class, 'getAllBooks']);

        // Gestion des commentaires (accessibles uniquement aux admins)
        Route::get('/comments', [CommentController::class, 'index']);
        Route::get('/comments/content/{content}', [CommentController::class, 'getByContent']);
    });
});
