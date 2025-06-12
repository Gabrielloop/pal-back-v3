<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Book;
use Illuminate\Http\Request;

class BookController extends Controller
{
    // GET /api/books/all
    public function getAllBooks()
    {
        return response()->json([
            'success' => true,
            'message' => 'Liste de tous les livres',
            'data' => Book::all()
        ]);
    }

    // GET /api/books/isbn/{isbn}
    public function getBookByIsbn($isbn)
    {
        $book = Book::findOrFail($isbn);
        return response()->json([
            'success' => true,
            'message' => 'Recherche par isbn : ' . $isbn,
            'data' => $book
        ]);
    }

    // GET /api/books/title/{title}
    public function getBooksByTitle($title)
    {
        $books = Book::where('book_title', 'like', '%' . $title . '%')->get();
        return response()->json([
            'success' => true,
            'message' => 'Recherche par titre : ' . $title,
            'data' => $books
        ]);
    }

    // POST /api/books/add
    public function saveBook(Request $request)
    {
        $validated = $request->validate([
            'isbn' => 'required|string|unique:books',
            'book_title' => 'required|string|max:255',
            'book_author' => 'required|string|max:255',
            'book_publisher' => 'nullable|string|max:255',
            'book_year' => 'nullable|integer',
        ]);

        $book = Book::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Livre ajouté',
            'data' => $book
        ]);
    }

    // PUT /api/books/update/{isbn}
    public function updateBook(Request $request, $isbn)
    {
        $book = Book::findOrFail($isbn);

        $validated = $request->validate([
            'book_title' => 'sometimes|string|max:255',
            'book_author' => 'sometimes|string|max:255',
            'book_publisher' => 'sometimes|nullable|string|max:255',
            'book_year' => 'sometimes|nullable|integer',
        ]);

        $book->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Livre modifié',
            'data' => $book
        ]);
    }

    // DELETE /api/books/delete/{isbn}
    public function deleteBook($isbn)
    {
        $book = Book::find($isbn);
        if ($book) {
            $book->delete();
            return response()->json([
                'success' => true,
                'message' => 'Livre supprimé',
                'data' => $book
            ]);
        }

        return response()->json(['message' => 'Livre non trouvé'], 404);
        
    }

    // GET /api/books/error
    public function error()
    {
        return response()->json(['message' => 'Erreur serveur'], 500);
    }
}
