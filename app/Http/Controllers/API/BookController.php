<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Book;
use Illuminate\Http\Request;

class BookController extends Controller
{

    public function index()
    {
        return response()->json([
            'success' => true,
            'message' => 'Liste de tous les livres',
            'data' => Book::all()
        ], 200);
    }

    public function show($isbn)
    {
        $book = Book::findOrFail($isbn);
        return response()->json([
            'success' => true,
            'message' => 'Recherche par isbn : ' . $isbn,
            'data' => $book
        ], 200);
    }

    public function showByTitle($title)
    {
        $books = Book::where('title', 'like', '%' . $title . '%')->get();
        return response()->json([
            'success' => true,
            'message' => 'Recherche par titre : ' . $title,
            'data' => $books
        ], 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'isbn' => 'required|string|unique:books',
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'publisher' => 'nullable|string|max:255',
            'year' => 'nullable|integer',
        ]);

        $book = Book::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Livre ajouté',
            'data' => $book
        ], 201);
    }

    public function update(Request $request, $isbn)
    {
        $book = Book::findOrFail($isbn);

        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'author' => 'sometimes|string|max:255',
            'publisher' => 'sometimes|nullable|string|max:255',
            'year' => 'sometimes|nullable|integer',
        ]);

        $book->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Livre modifié',
            'data' => $book
        ], 200);
    }

    public function destroy($isbn)
    {
        $book = Book::find($isbn);
        if ($book) {
            $book->delete();
            return response()->json([
                'success' => true,
                'message' => 'Livre supprimé',
                'data' => $book
            ], 200);
        }

        return response()->json(['message' => 'Livre non trouvé'], 404);
    }
}
