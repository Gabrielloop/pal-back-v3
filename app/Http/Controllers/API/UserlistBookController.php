<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Userlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserlistBookController extends Controller
{
    // GET /api/userlistBooks/all  (ADMIN)
    public function index()
    {
        $entries = DB::table('userlist_book')->get();

        // Grouper par ISBN
        $grouped = $entries->groupBy('isbn')->map(function ($items, $isbn) {
            $book = Book::where('isbn', $isbn)->first();

            // Récupérer les listes complètes
            $lists = Userlist::whereIn('userlist_id', $items->pluck('userlist_id'))->get();

            return [
                'isbn' => $book?->isbn,
                'book_title' => $book?->book_title,
                'book_author' => $book?->book_author,
                'book_publisher' => $book?->book_publisher,
                'book_year' => $book?->book_year,
                'lists' => $lists,
            ];
        })->values();

        return response()->json([
            'success' => true,
            'message' => 'Livres et leurs listes associées',
            'data' => $grouped,
        ], 200);
    }

    // DELETE /api/userlistBooks/id/{id}   (ADMIN)
    public function destroyById($id)
    {
        $deleted = DB::table('userlist_book')->where('id', $id)->delete();

        if (!$deleted) {
            return response()->json([
                'success' => false,
                'message' => 'Entrée non trouvée',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Entrée supprimée',
        ], 200);
    }
    // PUT /api/userlistBooks/id/{id}   (ADMIN)
    public function updateById(Request $request, $id)
    {
        $validated = $request->validate([
            'userlist_id' => 'required|exists:userlists,userlist_id',
            'isbn' => 'required|exists:books,isbn',
        ]);

        $updated = DB::table('userlist_book')
            ->where('id', $id)
            ->update([
                'userlist_id' => $validated['userlist_id'],
                'isbn' => $validated['isbn'],
                'updated_at' => now(),
            ]);

        if (!$updated) {
            return response()->json([
                'success' => false,
                'message' => 'Entrée non trouvée ou non mise à jour',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Entrée mise à jour',
            'data' => [
                'id' => $id,
                'userlist_id' => $validated['userlist_id'],
                'isbn' => $validated['isbn'],
            ]
        ], 200);
    }

    // POST /api/userlistBooks   (USER)
    public function store(Request $request)
    {
        $validated = $request->validate([
            'userlist_id' => 'required|exists:userlists,userlist_id',
            'isbn' => 'required|exists:books,isbn',
        ]);

        $userId = $request->user()->id;
        
        $userlist = Userlist::where('userlist_id', $validated['userlist_id'])
            ->where('user_id', $userId)
            ->first();

        if (!$userlist) {
            return response()->json([
                'success' => false,
                'message' => 'Liste non trouvée ou non autorisée',
            ], 403);
        }

        // Vérifie la présence du livre dans la liste
        if ($userlist->books()->where('userlist_book.isbn', $validated['isbn'])->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Le livre est déjà dans la liste',
            ], 409);
        }

            // Ajoute le livre via la relation
        $userlist->books()->attach($validated['isbn'], [
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Livre ajouté à la liste',
            'data' => [
                'userlist_id' => $validated['userlist_id'],
                'isbn' => $validated['isbn'],
            ]
        ], 201);
    }

    // DELETE /api/userlistBooks/{listId}/{isbn}   (USER)
    public function destroy(Request $request, $listId, $isbn)
    {
        $userlist = Userlist::where('userlist_id', $listId)
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$userlist) {
            return response()->json([
                'success' => false,
                'message' => 'Liste non trouvée ou non autorisée',
            ], 403);
        }

        $deleted = DB::table('userlist_book')
            ->where('userlist_id', $listId)
            ->where('isbn', $isbn)
            ->delete();

        if (!$deleted) {
            return response()->json([
                'success' => false,
                'message' => 'Livre non trouvé dans la liste',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Livre supprimé de la liste',
        ], 200);
    }
}
