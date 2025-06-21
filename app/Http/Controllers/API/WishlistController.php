<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Wishlist;
use App\Models\Book;
use Illuminate\Http\Request;
use App\Services\BookCacheService;

class WishlistController extends Controller
{
    // GET /api/wishlist/all   ADMIN
    public function getBooksWithUsersWhoWished()
    {
        $wishlists = Wishlist::with('book')->get();

        $grouped = $wishlists->groupBy('isbn')->map(function ($items) {
            $book = $items->first()->book;

            return [
                'isbn' => $book->isbn,
                'title' => $book->title,
                'author' => $book->author,
                'publisher' => $book->publisher,
                'year' => $book->year,
                'users' => $items->pluck('user_id')->unique()->values()
            ];
        })->values();

        return response()->json([
            'success' => true,
            'message' => 'Livres avec utilisateurs ayant mis en wishlist',
            'data' => $grouped,
        ], 200);
    }
        
    // GET /api/wishlist/collection   ADMIN
    public function getWishlistsCollection()
        {
            $wishlists = Wishlist::all();

            return response()->json([
                'success' => true,
                'message' => 'Liste des wishlists',
                'data' => $wishlists,
            ], 200);
        }

    // DELETE /api/wishlists/userid/{userid}/{isbn}   ADMIN
    public function destroyByUserIdAndIsbn($userid, $isbn)
    {
        $wishlist = Wishlist::where('user_id', $userid)
            ->where('isbn', $isbn)
            ->first();

        if (!$wishlist) {
            return response()->json([
                'success' => false,
                'message' => 'Wishlist non trouvée',
            ], 404);
        }

        $wishlist->delete();

        return response()->json([
            'success' => true,
            'message' => 'Wishlist supprimée',
            'data' => $wishlist,
        ], 200);
    }


    // GET /api/wishlists   USER
    public function getWishlists(Request $request)
    {
        $userId = $request->user()->id;
        $wishlists = Wishlist::with('book')->where('user_id', $userId)->get();


        return response()->json([
            'success' => true,
            'message' => "Wishlist de l'utilisateur",
            'data' => $wishlists->pluck('book'),
        ], 200);
    }

    // GET /api/wishlists/isbn/{isbn}    USER
    public function getWishlistByIsbn(Request $request, $isbn)
    {
        $userId = $request->user()->id;

        $wishlist = Wishlist::with('book')
            ->where('user_id', $userId)
            ->where('isbn', $isbn)
            ->first();

        if (!$wishlist) {
            return response()->json([
                'success' => false,
                'message' => 'Livre non trouvé en wishlist',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Livre trouvé en wishlist',
            'data' => $wishlist->book,
        ], 200);
    }

    // POST /api/wishlists/isbn/{isbn}   USER
    public function store(Request $request, $isbn)
    {
         $book = BookCacheService::ensurePersisted($isbn);

        if (!$book) {
            return response()->json([
                'success' => false,
                'message' => 'Livre introuvable dans le cache.',
            ], 404);
        }


        if (!Book::where('isbn', $isbn)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Livre non trouvé',
            ], 404);
        }

        if (Wishlist::where('user_id', $request->user()->id)
            ->where('isbn', $isbn)
            ->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Livre déjà en wishlist',
            ], 409);
        }

        $wishlists = Wishlist::create([
            'user_id' => $request->user()->id,
            'isbn' => $isbn,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Livre ajouté à la wishlist',
            'data' => $wishlists,
        ], 201);
    }

    // DELETE /api/wishlists/isbn/{isbn} USER
    public function destroy(Request $request, $isbn)
    {
        $userId = $request->user()->id;

        if (!Book::where('isbn', $isbn)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Livre non trouvé',
            ], 404);
        }

        $wishlist = Wishlist::where('user_id', $userId)
            ->where('isbn', $isbn)
            ->first();

        if (!$wishlist) {
            return response()->json([
                'success' => false,
                'message' => 'Livre non trouvé en wishlist',
            ], 404);
        }

        $wishlist->delete();

        return response()->json([
            'success' => true,
            'message' => 'Livre supprimé de la wishlist',
            'data' => $wishlist,
        ], 200);
    }
}
