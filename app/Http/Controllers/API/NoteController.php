<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Note;
use Illuminate\Http\Request;

class NoteController extends Controller
{
    // GET /api/notes/all   (ADMIN)
    public function index()
    {
        return response()->json([
            'success' => true,
            'message' => 'Liste de toutes les notes',
            'data' => Note::all(),
        ], 200);
    }

    // GET /api/notes/note/{note}   (USER)
    public function getBooksByUserAndNote(Request $request, $note)
{
    $userId = $request->user()->id;

    // Récupérer les notes avec le livre associé
    $notes = Note::with('book')
        ->where('user_id', $userId)
        ->where('note_content', $note)
        ->get();

    // Formater les données
    $data = $notes->map(function ($note) {
        return [
            'isbn' => $note->book->isbn,
            'title' => $note->book->book_title,
            'author' => $note->book->book_author,
            'publisher' => $note->book->book_publisher,
            'year' => $note->book->book_year,
            'note_content' => $note->note_content,
        ];
    });

    return response()->json([
        'success' => true,
        'message' => "Livres notés {$note} par l’utilisateur",
        'data' => $data,
    ],200);
}


    // POST /api/notes/isbn/{isbn}   (USER)
    public function store(Request $request)
    {
        $validated = $request->validate([
            'isbn' => 'required|string|exists:books,isbn',
            'note_content' => 'required|string',
        ]);

        $note = Note::create([
            'user_id' => $request->user()->id,
            'isbn' => $validated['isbn'],
            'note_content' => $validated['note_content'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Note ajoutée',
            'data' => $note,
        ], 201);
    }

    // PUT /api/notes/isbn/{isbn}  (USER)
    public function update(Request $request, $isbn)
    {
        $userId = $request->user()->id;

        $note = Note::where('isbn', $isbn)
            ->where('user_id', $userId)
            ->firstOrFail();

        $validated = $request->validate([
            'note_content' => 'required|string',
        ]);

        $note->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Note mise à jour',
            'data' => $note,
        ],200);
    }

    // DELETE /api/notes/isbn/{isbn}  (USER)
    public function destroy(Request $request, $isbn)
    {
        $userId = $request->user()->id;

        $note = Note::where('isbn', $isbn)
            ->where('user_id', $userId)
            ->first();

        if (!$note) {
            return response()->json([
                'success' => false,
                'message' => 'Note non trouvée',
            ], 404);
        }

        $note->delete();

        return response()->json([
            'success' => true,
            'message' => 'Note supprimée',
            'data' => $note,
        ],200);
    }
}
