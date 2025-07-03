<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Favorite;
use App\Models\Book;
use Illuminate\Http\Request;
use App\Services\BookCacheService;


class FavoriteController extends Controller
{
    public function getFavoritesCollection()
    {

        $favorites = Favorite::all();

        return response()->json([
            'success' => true,
            'message' => 'Liste des favoris',
            'data' => $favorites,
        ], 200);
    }

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

    public function store(Request $request, $isbn)
    {
        $book = BookCacheService::ensurePersisted($isbn);

        if (!$book) {
            return response()->json([
                'success' => false,
                'message' => 'Livre introuvable dans le cache.',
            ], 404);
        }

        if (Favorite::where('user_id', $request->user()->id)
            ->where('isbn', $isbn)
            ->exists()
        ) {
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
