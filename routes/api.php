<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;        // Authentification
use App\Http\Controllers\API\UserController;        // Gestion des utilisateurs
use App\Http\Controllers\API\BookController;        // Gestion des livres
use App\Http\Controllers\API\CommentController;     // Gestion des commentaires
use App\Http\Controllers\API\FavoriteController;    // Gestion des favoris
use App\Http\Controllers\API\WishlistController;    // Gestion des wishlists

// Routes publiques
Route::post('/user/login', [AuthController::class, 'login']);
Route::post('/user/users', [UserController::class, 'store']);

// Routes pour utilisateurs authentifiés
Route::middleware('auth:sanctum')->group(function () {

    // Gestion des utilisateurs (accessibles à tous les utilisateurs connectés)
    Route::get('/users/me', [UserController::class, 'me']);
    Route::put('/users/me', [UserController::class, 'updateMe']);
    Route::post('/users/logout', [AuthController::class, 'logout']);

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

    // Gestion des favoris (accessibles à tous les utilisateurs connectés)
    Route::get('/favorites', [FavoriteController::class, 'getFavorites']);
    Route::get('/favorites/isbn/{isbn}', [FavoriteController::class, 'getFavoriteByIsbn']);
    Route::post('/favorites/isbn/{isbn}', [FavoriteController::class, 'store']);
    Route::delete('/favorites/isbn/{isbn}', [FavoriteController::class, 'destroy']);

    // gestion des wishlists (accessibles à tous les utilisateurs connectés)
    Route::get('/wishlists', [WishlistController::class, 'getWishlists']);
    Route::get('/wishlists/isbn/{isbn}', [WishlistController::class, 'getWishlistByIsbn']);
    Route::post('/wishlists/isbn/{isbn}', [WishlistController::class, 'store']);
    Route::delete('/wishlists/isbn/{isbn}', [WishlistController::class, 'destroy']);

    // Routes réservées aux admins
    Route::middleware(['auth:sanctum', 'is_admin'])->group(function () {

        // Gestion des utilisateurs (accessibles uniquement aux admins)
        Route::get('/users', [UserController::class, 'index']);
        Route::get('/users/{id}', [UserController::class, 'show']);
        Route::put('/users/{id}', [UserController::class, 'update']);
        Route::delete('users/{id}', [UserController::class, 'destroy']);

        // Gestion des livres (accessibles uniquement aux admins)
        Route::get('/books/all', [BookController::class, 'getAllBooks']);

        // Gestion des commentaires (accessibles uniquement aux admins)
        Route::get('/comments/all', [CommentController::class, 'index']);
        Route::get('/comments/content/{content}', [CommentController::class, 'getByContent']);

        // Gestion des favoris (accessibles uniquement aux admins)
        Route::get('/favorites/all', [FavoriteController::class, 'getBooksWithUsersWhoFavorited']);

        // Gestion des wishlists (accessibles uniquement aux admins)
        Route::get('/wishlists/all', [WishlistController::class, 'getBooksWithUsersWhoWished']);
    });
});
