<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;            // Authentification
use App\Http\Controllers\API\UserController;            // Gestion des utilisateurs
use App\Http\Controllers\API\BookController;            // Gestion des livres
use App\Http\Controllers\API\CommentController;         // Gestion des commentaires
use App\Http\Controllers\API\FavoriteController;        // Gestion des favoris
use App\Http\Controllers\API\WishlistController;        // Gestion des wishlists
use App\Http\Controllers\API\UserlistController;        // Gestion des userlists
use App\Http\Controllers\API\UserlistBookController;    // Gestion des livres dans les userlists
use App\Http\Controllers\API\NoteController;            // Gestion des notes
use App\Http\Controllers\API\ReadingController;         // Gestion des lectures

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
    Route::put('/books/isbn/{isbn}', [BookController::class, 'updateBook']);
    Route::delete('/books/isbn/{isbn}', [BookController::class, 'deleteBook']);

    // Gestion des commentaires (accessibles à tous les utilisateurs connectés)
    Route::get('/comments/isbn/{isbn}', [CommentController::class, 'getByIsbnForCurrentUser']);
    Route::post('/comments', [CommentController::class, 'store']);
    Route::put('/comments/isbn/{isbn}', [CommentController::class, 'update']);
    Route::delete('/comments/isbn/{isbn}', [CommentController::class, 'destroy']);

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

    // gestion des userlists (accessibles à tous les utilisateurs connectés)
    Route::get('/userlists', [UserlistController::class, 'getUserLists']);
    Route::get('/userlists/id/{id}', [UserlistController::class, 'show']);
    Route::get('/userlists/title/{title}', [UserlistController::class, 'getByTitle']);
    Route::post('/userlists', [UserlistController::class, 'store']);
    Route::put('/userlists/id/{id}', [UserlistController::class, 'update']);
    Route::delete('/userlists/id/{id}', [UserlistController::class, 'destroy']);

    // Gestion des livres dans les userlists (accessibles à tous les utilisateurs connectés)
    Route::get('/userlistBooks', [UserlistBookController::class, 'getBooksByListId']);
    Route::post('/userlistBooks', [UserlistBookController::class, 'store']);
    Route::delete('/userlistBooks/{listId}/{isbn}', [UserlistBookController::class, 'destroy']);

    // Gestion des notes (accessibles à tous les utilisateurs connectés)
    Route::get('/notes/note/{note}', [NoteController::class, 'getBooksByUserAndNote']);
    Route::post('/notes/isbn/{isbn}', [NoteController::class, 'storeOrUpdateOrDelete']);

    // Gestion des lectures (accessibles à tous les utilisateurs connectés)
    Route::get('/reading/all', [ReadingController::class, 'index']);
    Route::get('/reading/notStarted', [ReadingController::class, 'getNotStarted']);
    Route::get('/reading/reading', [ReadingController::class, 'getReading']);
    Route::get('/reading/finished', [ReadingController::class, 'getFinished']);
    Route::get('/reading/abandoned', [ReadingController::class, 'getAbandoned']);
    Route::post('/reading/add/{isbn}', [ReadingController::class, 'add']);
    Route::post('/reading/set/{isbn}', [ReadingController::class, 'setProgress']);
    Route::post('/reading/abandon/{isbn}', [ReadingController::class, 'abandon']);

    // Routes réservées aux admins
    Route::middleware(['auth:sanctum', 'is_admin'])->group(function () {

        // Gestion des utilisateurs (accessibles uniquement aux admins)
        Route::get('/users', [UserController::class, 'index']);
        Route::get('/users/{id}', [UserController::class, 'show']);
        Route::put('/users/{id}', [UserController::class, 'update']);
        Route::delete('users/{id}', [UserController::class, 'destroy']);

        // Gestion des livres (accessibles uniquement aux admins)
        Route::get('/books/all', [BookController::class, 'getAllBooks']);
        Route::delete('/books/isbn/{isbn}', [BookController::class, 'deleteBook']);

        // Gestion des commentaires (accessibles uniquement aux admins)
        Route::get('/comments/all', [CommentController::class, 'index']);
        Route::get('/comments/content/{content}', [CommentController::class, 'getByContent']);
        Route::put('/comments/{id}', [CommentController::class, 'update']);
        Route::delete('/comments/{id}', [CommentController::class, 'destroy']);

        // Gestion des favoris (accessibles uniquement aux admins)
        Route::get('/favorites/all', [FavoriteController::class, 'getBooksWithUsersWhoFavorited']);
        Route::delete('/favorites/id/{id}', [FavoriteController::class, 'destroy']);

        // Gestion des wishlists (accessibles uniquement aux admins)
        Route::get('/wishlists/all', [WishlistController::class, 'getBooksWithUsersWhoWished']);
        Route::delete('/wishlists/id/{id}', [WishlistController::class, 'destroy']);
        Route::put('/wishlists/id/{id}', [WishlistController::class, 'updateById']);

        // Gestion des userlists (accessibles uniquement aux admins)
        Route::get('/userlists/all', [UserlistController::class, 'index']);
        Route::put('/userlists/id/{id}', [UserlistController::class, 'updateById']);
        Route::delete('/userlists/id/{id}', [UserlistController::class, 'destroyById']);

        // Gestion des livres dans les userlists (accessibles uniquement aux admins)
        Route::get('/userlistBooks/all', [UserlistBookController::class, 'index']);
        Route::delete('/userlistBooks/id/{id}', [UserlistBookController::class, 'destroyById']);
        Route::put('/userlistBooks/id/{id}', [UserlistBookController::class, 'updateById']);

        // Gestion des notes (accessibles uniquement aux admins)
        Route::get('/notes/all', [NoteController::class, 'index']);
        Route::delete('/notes/id/{id}', [NoteController::class, 'destroy']);

        // Gestion des lectures (accessibles uniquement aux admins)
        Route::get('/reading/all', [ReadingController::class, 'index']);

    });
});
