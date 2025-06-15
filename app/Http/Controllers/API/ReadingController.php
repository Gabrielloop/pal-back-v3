<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Reading;
use App\Models\Book;
use Illuminate\Http\Request;

class ReadingController extends Controller
{
    // GET /api/reading/all   (ADMIN)
    public function index()
    {
        return response()->json([
            'success' => true,
            'message' => 'Liste de toutes les lectures en cours',
            'data' => Reading::all(),
        ], 200);
    }

    // GET /api/reading/notStarted   (USER)
    public function getNotStarted(Request $request)
    {
        $books = Book::where('user_id', $request->user()->id)
            ->where('reading_percent', 0)
            ->get();

        return $this->formatResponse($books, 'Livres non commencés');
    }

    // GET /api/reading/reading   (USER)
    public function getReading(Request $request)
    {
        $books = Book::where('user_id', $request->user()->id)
            ->whereBetween('reading_percent', [1, 99])
            ->get();

        return $this->formatResponse($books, 'Livres en cours de lecture');
    }

    // GET /api/reading/finished   (USER)
    public function getFinished(Request $request)
    {
        $books = Book::where('user_id', $request->user()->id)
            ->where('reading_percent', 100)
            ->get();

        return $this->formatResponse($books, 'Livres terminés');
    }

    private function formatResponse($books, $message)
    {
        $data = $books->map(function ($book) {
            return [
                'isbn' => $book->isbn,
                'book_title' => $book->book_title,
                'book_author' => $book->book_author,
                'book_publisher' => $book->book_publisher,
                'book_year' => $book->book_year,
                'reading_percent' => $book->reading_percent,
            ];
        });

        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ]);
    }

    // POST /api/reading/isbn/{isbn}  (USER)
    public function storeOrUpdateOrDelete(Request $request, $isbn)
    {
        $validated = $request->validate([
            'reading_content' => 'required|string',
        ]);

        $userId = $request->user()->id;

        $reading = Reading::where('user_id', $request->user()->id)
            ->where('isbn', $isbn)
            ->first();

        // Si reading_content est "0", on supprime l'avancement
        if ($validated['reading_content'] === "0")  {
            if ($reading) {
                $reading->delete();

                return response()->json([
                    'success' => true,
                    'message' => 'Non lu',
                ], 200);
            }

            return response()->json([
                'success' => false,
                'message' => 'Livre introuvable',
            ], 404);
        }

        // Si l'avancement existe, on la met à jour
        if ($reading) {
            $reading->update([
                'reading_content' => $validated['reading_content'],
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Lecture mise à jour',
                'data' => $reading,
            ], 200);
        }

        // Sinon on la crée
        $newReading = Reading::create([
            'user_id' => $userId,
            'isbn' => $isbn,
            'reading_content' => $validated['reading_content'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Lecture ajoutée',
            'data' => $newReading,
        ], 201);
    }
}
