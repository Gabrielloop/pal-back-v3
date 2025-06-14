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

    // POST /api/notes/isbn/{isbn}  (USER)
    public function storeOrUpdateOrDelete(Request $request, $isbn)
    {
        $validated = $request->validate([
            'note_content' => 'required|string',
        ]);

        $userId = $request->user()->id;

        $note = Note::where('user_id', $userId)
                    ->where('isbn', $isbn)
                    ->first();

        // Si note_content est "0", on supprime la note si elle existe
        if ($validated['note_content'] === "0") {
            if ($note) {
                $note->delete();

                return response()->json([
                    'success' => true,
                    'message' => 'Note supprimée (0)',
                ], 200);
            }

            return response()->json([
                'success' => false,
                'message' => 'Aucune note à supprimer',
            ], 404);
        }

        // Si la note existe, on la met à jour
        if ($note) {
            $note->update([
                'note_content' => $validated['note_content'],
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Note mise à jour',
                'data' => $note,
            ], 200);
        }

        // Sinon on la crée
        $newNote = Note::create([
            'user_id' => $userId,
            'isbn' => $isbn,
            'note_content' => $validated['note_content'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Note ajoutée',
            'data' => $newNote,
        ], 201);
    }
}
