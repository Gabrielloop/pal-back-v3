<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Reading;
use App\Models\Book;
use Illuminate\Http\Request;
use App\Services\BookCacheService;

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
        $reading = Reading::where('user_id', $userid)->where('isbn', $isbn)->first();

        if (!$reading) {
            return response()->json(['success' => false, 'message' => 'Lecture non trouvée'], 404);
        }

        $reading->delete();

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
            'started_at' => 'nullable|date',
            'finished_at' => 'nullable|date',
        ]);


   $validated['started_at'] = $validated['started_at'] === '' ? null : $validated['started_at'];
    $validated['finished_at'] = $validated['finished_at'] === '' ? null : $validated['finished_at'];


         $reading->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Lecture mise à jour',
            'data' => $this->formatSingleReading($reading),
        ]);
    }


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
                'started_at' => $reading->started_at?->format('Y-m-d') ?? null,
                'finished_at' => $reading->finished_at?->format('Y-m-d') ?? null,
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

        $book = BookCacheService::ensurePersisted($isbn);

        if (!$book) {
            return response()->json([
                'success' => false,
                'message' => 'Livre introuvable dans le cache.',
            ], 404);
        }

        $validated = $request->validate([
        'started_at' => $validated['started_at'],
        'finished_at' => $validated['finished_at'],
            ]);

   $validated['started_at'] = $validated['started_at'] === '' ? null : $validated['started_at'];
    $validated['finished_at'] = $validated['finished_at'] === '' ? null : $validated['finished_at'];


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
                'started_at' => $request->started_at,
                'finished_at' => $request->finished_at,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Lecture initialisée',
            'data' => $this->formatSingleReading($reading),
        ]);
    }

    // POST /api/reading/set/{isbn}
    public function setProgress(Request $request, $isbn)
    {

            $request->merge([
        'started_at' => $request->started_at === '' ? null : $request->started_at,
        'finished_at' => $request->finished_at === '' ? null : $request->finished_at,
        ]);

        $validated = $request->validate([
        'reading_content' => 'required|integer|min:0|max:100',
        'started_at' => 'nullable|date',
        'finished_at' => 'nullable|date',
            ]);


        $userId = $request->user()->id;

        $isbn = $request->input('isbn', $isbn);
        $book = BookCacheService::ensurePersisted($isbn);

        if (!$book) {
            return response()->json([
                'success' => false,
                'message' => 'Livre introuvable dans le cache.',
            ], 404);
        }

         // Calcul des états de lecture
            $progress = (int) $validated['reading_content'];
            $isStarted = $progress > 0;
            $isReading = $progress > 0 && $progress < 100;
            $isFinished = $progress === 100;
            

            // Création ou mise à jour de la lecture
            $reading = Reading::updateOrCreate(
                ['user_id' => $userId, 'isbn' => $isbn],
                [
                    'reading_content' => $progress,
                    'is_started' => $isStarted,
                    'is_reading' => $isReading,
                    'is_finished' => $isFinished,
                    'is_abandoned' => false,
                    'started_at' => $validated['started_at'],
                    'finished_at' => $validated['finished_at'],
                ]
            );

            // Rechargement avec la relation `book`
            $reading->load('book');

            return response()->json([
                'success' => true,
                'message' => 'Avancement mis à jour',
                'data' => $this->formatSingleReading($reading),
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
                'started_at' => optional($reading->started_at)->format('Y-m-d'),
                'finished_at' => optional($reading->finished_at)->format('Y-m-d'),
            ];
        }

    // POST /api/reading/abandon/{isbn}
    public function abandon(Request $request, $isbn)
    {
        $userId = $request->user()->id;

         $book = BookCacheService::ensurePersisted($isbn);

        if (!$book) {
            return response()->json([
                'success' => false,
                'message' => 'Livre introuvable dans le cache.',
            ], 404);
        }
        
        $reading = Reading::where('user_id', $userId)->where('isbn', $isbn)->first();

        if (!$reading) {
            return response()->json(['success' => false, 'message' => 'Lecture non trouvée'], 404);
        }

        $reading->update([
        'reading_content' => 0,
        'is_started' => false,
        'is_reading' => false,
        'is_finished' => false,
        'is_abandoned' => true,
    ]);

    $reading->refresh();

        return response()->json([
            'success' => true,
            'message' => 'Lecture abandonnée',
            'data' => $this->formatSingleReading($reading),
        ]);
    }
}
