<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Reading;
use App\Models\Book;
use Illuminate\Http\Request;

class ReadingController extends Controller
{
    public function index()
    {
        return response()->json([
            'success' => true,
            'message' => 'Liste de toutes les lectures en cours',
            'data' => Reading::all(),
        ]);
    }

    // DELETE /api/reading/userid/{userid}/{isbn}   (ADMIN)
    public function destroyByUserIdAndIsbn($userid, $isbn)
    {
        $reading = Reading::where('user_id', $userid)
            ->where('isbn', $isbn)
            ->first();

        if (!$reading) {
            return response()->json(['success' => false, 'message' => 'Lecture non trouvée'], 404);
        }

        $reading->where('user_id', $userid)
            ->where('isbn', $isbn)
            ->delete();

        return response()->json(['success' => true, 'message' => 'Lecture supprimée']);
    }

    // PUT /api/reading/userid/{userid}/{isbn}   (ADMIN)
    public function updateByUserIdAndIsbn(Request $request, $userid, $isbn)
    {
        $reading = Reading::where('user_id', $userid)
            ->where('isbn', $isbn)
            ->firstOrFail();

        $validated = $request->validate([
            'reading_content' => 'required|integer|min:0|max:100',
            'is_started' => 'required|boolean',
            'is_reading' => 'required|boolean',
            'is_finished' => 'required|boolean',
            'is_abandoned' => 'required|boolean',
        ]);

        // TODO : maj de la réponse pour envoyer l'objet modifié
        $reading->where('user_id', $userid)
            ->where('isbn', $isbn)
            ->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Lecture mise à jour',
            'data' => $reading,
        ]);
    }

    // GET /api/reading/notStarted   (USER)

    public function getNotStarted(Request $request)
    {
        $readings = Reading::with('book')
            ->where('user_id', $request->user()->id)
            ->where('reading_content', 0)
            ->where('is_started', false)
            ->where('is_finished', false)
            ->where('is_abandoned', false)
            ->get();

        return $this->formatResponse($readings, 'Livres non commencés');
    }

    public function getReading(Request $request)
    {
        $readings = Reading::with('book')
            ->where('user_id', $request->user()->id)
            ->whereBetween('reading_content', [1, 99])
            ->get();

        return $this->formatResponse($readings, 'Livres en cours de lecture');
    }

    public function getFinished(Request $request)
    {
        $readings = Reading::with('book')
            ->where('user_id', $request->user()->id)
            ->where('reading_content', 100)
            ->get();

        return $this->formatResponse($readings, 'Livres terminés');
    }

    public function getAbandoned(Request $request)
    {
        $readings = Reading::with('book')
            ->where('user_id', $request->user()->id)
            ->where('is_abandoned', true)
            ->get();

        return $this->formatResponse($readings, 'Livres abandonnés');
    }

    private function formatResponse($readings, $message)
    {
        $data = $readings->map(function ($reading) {
            $book = $reading->book;
            return [
                'isbn' => $reading->isbn,
                'title' => $book->title,
                'author' => $book->author,
                'publisher' => $book->publisher,
                'year' => $book->year,
                'reading_content' => $reading->reading_content,
                'is_started' => $reading->is_started,
                'is_reading' => $reading->is_reading,
                'is_finished' => $reading->is_finished,
                'is_abandoned' => $reading->is_abandoned,
            ];
        });

        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ]);
    }

    // POST /api/reading/add/{isbn}
    public function add(Request $request, $isbn)
    {
        $userId = $request->user()->id;

        $book = Book::where('isbn', $isbn)->first();
        if (!$book) {
            return response()->json(['success' => false, 'message' => 'Livre inexistant'], 404);
        }

        $reading = Reading::updateOrCreate(
            ['user_id' => $userId, 'isbn' => $isbn],
            [
                'reading_content' => 0,
                'is_started' => false,
                'is_reading' => false,
                'is_finished' => false,
                'is_abandoned' => false,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Lecture initialisée',
            'data' => $reading,
        ]);
    }

    // POST /api/reading/set/{isbn}
    public function setProgress(Request $request, $isbn)
    {
        $validated = $request->validate([
            'reading_content' => 'required|integer|min:0|max:100',
        ]);

        $userId = $request->user()->id;

        $book = Book::where('isbn', $isbn)->first();
        if (!$book) {
            return response()->json(['success' => false, 'message' => 'Livre inexistant'], 404);
        }

        $reading = Reading::where('user_id', $userId)->where('isbn', $isbn)->first();

        if (!$reading) {
            return response()->json(['success' => false, 'message' => 'Lecture non trouvée'], 404);
        }

        $progress = $validated['reading_content'];

        Reading::where('user_id', $userId)
            ->where('isbn', $isbn)
            ->update([
            'reading_content' => $progress,
            'is_started' => $progress > 0 && $progress < 100,
            'is_reading' => $progress > 0 && $progress < 100,
            'is_finished' => $progress === 100,
            'is_abandoned' => false,
        ]);

        $reading = Reading::with('book')
            ->where('user_id', $userId)
            ->where('isbn', $isbn)
            ->first();

        return response()->json([
            'success' => true,
            'message' => 'Avancement mis à jour',
            'data' => $this->formatSingleReading($reading)
        ]);
    }

    private function formatSingleReading($reading)
        {
            $book = $reading->book;

            return [
                'isbn' => $reading->isbn,
                'title' => $book?->title,
                'author' => $book?->author,
                'publisher' => $book?->publisher,
                'year' => $book?->year,
                'reading_content' => $reading->reading_content,
                'is_started' => $reading->is_started,
                'is_reading' => $reading->is_reading,
                'is_finished' => $reading->is_finished,
                'is_abandoned' => $reading->is_abandoned,
            ];
        }

    // POST /api/reading/abandon/{isbn}
    public function abandon(Request $request, $isbn)
    {
        $userId = $request->user()->id;

        $reading = Reading::where('user_id', $userId)->where('isbn', $isbn)->first();

        if (!$reading) {
            return response()->json(['success' => false, 'message' => 'Lecture non trouvée'], 404);
        }

        $reading->where('user_id', $userId)
            ->where('isbn', $isbn)
            ->update([
            'reading_content' => 0,
            'is_started' => false,
            'is_reading' => false,
            'is_finished' => false,
            'is_abandoned' => true,
        ]);

         $reading = $reading->where('user_id', $userId)
            ->where('isbn', $isbn)
            ->first();

        return response()->json([
            'success' => true,
            'message' => 'Lecture abandonnée',
            'data' => $reading,
        ]);
    }
}
