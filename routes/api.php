<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\BookController;
use App\Http\Controllers\API\CommentController;
use App\Http\Controllers\API\FavoriteController;
use App\Http\Controllers\API\WishlistController;
use App\Http\Controllers\API\UserlistController;
use App\Http\Controllers\API\UserlistBookController;
use App\Http\Controllers\API\NoteController;
use App\Http\Controllers\API\ReadingController;
use App\Http\Controllers\API\BnfProxyController;
use App\Http\Controllers\API\CoverController;;

// PUBLIC
Route::post('/user/login', [AuthController::class, 'login']);
Route::post('/user/users', [UserController::class, 'store']);
    Route::get('/cover/{isbn}', [CoverController::class, 'proxy']);

// AUTH
Route::middleware('auth:sanctum')->group(function () {

    Route::get('/bnf', [BnfProxyController::class, 'proxy']);

    // Gestion des utilisateurs (accessibles à tous les utilisateurs connectés)
    Route::get('/users/me', [UserController::class, 'me']);
    Route::put('/users/me', [UserController::class, 'updateMe']);
    Route::post('/users/logout', [AuthController::class, 'logout']);

    Route::get('/books/isbn/{isbn}', [BookController::class, 'getBookByIsbn']);
    Route::get('/books/title/{title}', [BookController::class, 'getBooksByTitle']);

    Route::get('/comments', [CommentController::class, 'getCommentForUser']);
    Route::get('/comments/isbn/{isbn}', [CommentController::class, 'getByIsbnForCurrentUser']);
    Route::post('/comments', [CommentController::class, 'addOrUpdateComment']);
    Route::delete('/comments/isbn/{isbn}', [CommentController::class, 'destroy']);

    Route::get('/favorites', [FavoriteController::class, 'getFavorites']);
    Route::get('/favorites/isbn/{book}', [FavoriteController::class, 'getFavoriteByIsbn']);
    Route::post('/favorites/isbn/{book}', [FavoriteController::class, 'store']);
    Route::delete('/favorites/isbn/{book}', [FavoriteController::class, 'destroy']);

    Route::get('/wishlists', [WishlistController::class, 'getWishlists']);
    Route::get('/wishlists/isbn/{isbn}', [WishlistController::class, 'getWishlistByIsbn']);
    Route::post('/wishlists/isbn/{isbn}', [WishlistController::class, 'store']);
    Route::delete('/wishlists/isbn/{isbn}', [WishlistController::class, 'destroy']);

    Route::get('/userlists', [UserlistController::class, 'getUserLists']);
    Route::get('/userlists/id/{id}', [UserlistController::class, 'show']);
    Route::get('/userlists/title/{title}', [UserlistController::class, 'getByTitle']);
    Route::post('/userlists', [UserlistController::class, 'store']);
    Route::put('/userlists/id/{id}', [UserlistController::class, 'update']);
    Route::delete('/userlists/id/{id}', [UserlistController::class, 'destroy']);

    Route::get('/userlistBooks', [UserlistBookController::class, 'getBooksByListId']);
    Route::post('/userlistBooks', [UserlistBookController::class, 'store']);
    Route::delete('/userlistBooks/{listId}/{isbn}', [UserlistBookController::class, 'destroy']);

    Route::get('/notes', [NoteController::class, 'getBooksByUserAndNote']);
    Route::post('/notes/isbn/{isbn}', [NoteController::class, 'storeOrUpdateOrDelete']);

    Route::get('/reading/all', [ReadingController::class, 'index']);
    Route::get('/reading/notStarted', [ReadingController::class, 'getNotStarted']);
    Route::get('/reading/reading', [ReadingController::class, 'getReading']);
    Route::get('/reading/finished', [ReadingController::class, 'getFinished']);
    Route::get('/reading/abandoned', [ReadingController::class, 'getAbandoned']);
    Route::post('/reading/add/{isbn}', [ReadingController::class, 'add']);
    Route::post('/reading/set/{isbn}', [ReadingController::class, 'setProgress']);
    Route::post('/reading/abandon/{isbn}', [ReadingController::class, 'abandon']);

    // ADMIN
    Route::middleware(['auth:sanctum', 'is_admin'])->group(function () {

        Route::get('/users', [UserController::class, 'index']);
        Route::get('/users/{id}', [UserController::class, 'show']);
        Route::put('/users/{id}', [UserController::class, 'update']);
        Route::delete('users/{id}', [UserController::class, 'destroy']);

        Route::get('/books/all', [BookController::class, 'getAllBooks']);
        Route::post('/books', [BookController::class, 'saveBook']);
        Route::put('/books/isbn/{isbn}', [BookController::class, 'updateBook']);
        Route::delete('/books/isbn/{isbn}', [BookController::class, 'deleteBook']);

        Route::get('/comments/all', [CommentController::class, 'index']);
        Route::get('/comments/content/{content}', [CommentController::class, 'getByContent']);
        Route::put('/comments/userid/{userid}/{isbn}/', [CommentController::class, 'updateByUserIdAndIsbn']);
        Route::delete('/comments/userid/{userid}/{isbn}/', [CommentController::class, 'destroyByUserIdAndIsbn']);

        Route::get('/favorites/all', [FavoriteController::class, 'getBooksWithUsersWhoFavorited']);
        Route::get('/favorites/collection', [FavoriteController::class, 'getFavoritesCollection']);
        Route::delete('/favorites/userid/{userid}/{isbn}', [FavoriteController::class, 'destroyByUserIdAndIsbn']);

        Route::get('/wishlists/all', [WishlistController::class, 'getBooksWithUsersWhoWished']);
        Route::get('/wishlists/collection', [WishlistController::class, 'getWishlistsCollection']);
        Route::delete('/wishlists/userid/{userid}/{isbn}', [WishlistController::class, 'destroyByUserIdAndIsbn']);

        Route::get('/userlists/all', [UserlistController::class, 'index']);
        Route::put('/userlists/userlistid/{userlistid}', [UserlistController::class, 'updateUserlistByUserId']);
        Route::delete('/userlists/userlistid/{userlistid}', [UserlistController::class, 'deleteUserlistByUserId']);
        
        Route::get('/userlistBooks/all', [UserlistBookController::class, 'getBooksWithUserList']);
        Route::get('/userlistBooks/collection', [UserlistBookController::class, 'index']);
        Route::delete('/userlistBooks/userlistid/{userlistid}/{isbn}', [UserlistBookController::class, 'deleteByUserlistId']);

        Route::get('/notes/all', [NoteController::class, 'index']);
        Route::put('/notes/userid/{userid}/{isbn}', [NoteController::class, 'updateByUserIdAndIsbn']);
        Route::delete('/notes/userid/{userid}/{isbn}', [NoteController::class, 'deleteByUserIdAndIsbn']);

        Route::get('/readings/all', [ReadingController::class, 'index']);
        Route::put('/readings/userid/{userid}/{isbn}', [ReadingController::class, 'updateByUserIdAndIsbn']);
        Route::delete('/readings/userid/{userid}/{isbn}', [ReadingController::class, 'destroyByUserIdAndIsbn']);

    });
});
