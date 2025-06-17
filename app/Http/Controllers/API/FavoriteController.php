<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Favorite;
use App\Models\Book;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    // GET /api/favorites/all   ADMIN
    public function getBooksWithUsersWhoFavorited()
    {
        $favorites = Favorite::with('book')->get();

        $grouped = $favorites->groupBy('isbn')->map(function ($items) {
            $book = $items->first()->book;

            return [
                'isbn' => $book->isbn,
                'book_title' => $book->book_title,
                'book_author' => $book->book_author,
                'book_publisher' => $book->book_publisher,
                'book_year' => $book->book_year,
                'users' => $items->pluck('user_id')->unique()->values()
            ];
        })->values();

        return response()->json([
            'success' => true,
            'message' => 'Livres avec utilisateurs ayant mis en favori',
            'data' => $grouped,
        ], 200);
    }

    // GET /api/favorites/collection   ADMIN
    public function getFavoritesCollection()
    {

        $favorites = Favorite::all();

        return response()->json([
            'success' => true,
            'message' => 'Liste des favoris',
            'data' => $favorites,
        ], 200);
    }

    // DELETE /api/favorites/userid/{userid}/{isbn}   ADMIN
    public function destroyByUserIdAndIsbn($userid, $isbn)
    {
        $favorite = Favorite::where('user_id', $userid)
            ->where('isbn', $isbn)
            ->first();

        if (!$favorite) {
            return response()->json([
                'success' => false,
                'message' => 'Favori non trouvé',
            ], 404);
        }

        $favorite->delete();

        return response()->json([
            'success' => true,
            'message' => 'Favori supprimé',
            'data' => $favorite,
        ], 200);
    }

    // GET /api/favorites   USER
    public function getFavorites(Request $request)
    {
        $userId = $request->user()->id;
        $favorites = Favorite::with('book')->where('user_id', $userId)->get();


        return response()->json([
            'success' => true,
            'message' => 'Favoris de l’utilisateur',
            'data' => $favorites->pluck('book'),
        ], 200);
    }

    // GET /api/favorites/isbn/{isbn}    USER
    public function getFavoriteByIsbn(Request $request, $isbn)
    {
        $userId = $request->user()->id;

        $favorites = Favorite::with('book')
            ->where('user_id', $userId)
            ->where('isbn', $isbn)
            ->first();

        if (!$favorites) {
            return response()->json([
                'success' => false,
                'message' => 'Favori non trouvé',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Favori trouvé',
            'data' => $favorites->book,
        ], 200);
    }

    // POST /api/favorites/isbn/{isbn}   USER
    public function store(Request $request, $isbn)
    {

        if (!Book::where('isbn', $isbn)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Livre non trouvé',
            ], 404);
        }

        if (Favorite::where('user_id', $request->user()->id)
            ->where('isbn', $isbn)
            ->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Favori déjà existant',
            ], 409);
        }

        $favorite = Favorite::create([
            'user_id' => $request->user()->id,
            'isbn' => $isbn,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Favori ajouté',
            'data' => $favorite,
        ], 201);
    }

    // DELETE /api/favorites/isbn/{isbn} USER
    public function destroy(Request $request, $isbn)
    {
        $userId = $request->user()->id;

        if (!Book::where('isbn', $isbn)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Livre non trouvé',
            ], 404);
        }

        $favorite = Favorite::where('user_id', $userId)
            ->where('isbn', $isbn)
            ->first();

        if (!$favorite) {
            return response()->json([
                'success' => false,
                'message' => 'Favori non trouvé',
            ], 404);
        }

        $favorite->delete();

        return response()->json([
            'success' => true,
            'message' => 'Favori supprimé',
            'data' => $favorite,
        ], 200);
    }
}
