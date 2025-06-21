<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Note;
use Illuminate\Http\Request;
use App\Services\BookCacheService;

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

    // PUT /api/notes/userid/{userid}/{isbn}   (ADMIN)
    public function updateByUserIdAndIsbn(Request $request, $userid, $isbn)
    {
        $validated = $request->validate([
            'note_content' => 'required|string',
        ]);

        // Vérifier si la note existe déjà
        $note = Note::where('user_id', $userid)->where('isbn', $isbn)->first();

        if (!$note) {
            return response()->json([
                'success' => false,
                'message' => 'Note non trouvée pour cet utilisateur et cet ISBN.',
            ], 404);
        }

        // Mettre à jour la note
        $note->update([
            'note_content' => $validated['note_content'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Note mise à jour avec succès.',
            'data' => $note,
        ], 200);
    }


    // DELETE /api/notes/userid/{userid}/{isbn}   (ADMIN)
    public function deleteByUserIdAndIsbn($userid, $isbn)
    {
        $note = Note::where('user_id', $userid)->where('isbn', $isbn)->first();

        if (!$note) {
            return response()->json([
                'success' => false,
                'message' => 'Note non trouvée pour cet utilisateur et cet ISBN.',
            ], 404);
        }

        $note->delete();

        return response()->json([
            'success' => true,
            'message' => 'Note supprimée avec succès.',
        ], 200);
    }

    // GET /api/notes/   (USER)
    public function getBooksByUserAndNote(Request $request)
    {

        $userId = $request->user()->id;

        // Récupérer les notes avec le livre associé
        $notes = Note::with('book')
            ->where('user_id', $userId)
            ->get();

        // Formater les données
        $data = $notes->map(function ($note) {
            return [
                'isbn' => $note->book->isbn,
                'title' => $note->book->title,
                'author' => $note->book->author,
                'publisher' => $note->book->publisher,
                'year' => $note->book->year,
                'note_content' => $note->note_content,
            ];
        });

        return response()->json([
            'success' => true,
            'message' => "Livres notés par l’utilisateur",
            'data' => $data,
        ],200);
    }

    // POST /api/notes/isbn/{isbn}  (USER)
    public function storeOrUpdateOrDelete(Request $request, $isbn)
    {
        $isbn = $request->input('isbn', $isbn);
        $book = BookCacheService::ensurePersisted($isbn);

        if (!$book) {
            return response()->json([
                'success' => false,
                'message' => 'Livre introuvable dans le cache.',
            ], 404);
        }



        $validated = $request->validate([
            'note_content' => 'required|string',
        ]);

        $userId = $request->user()->id;

        $note = Note::where('user_id', $request->user()->id)
            ->where('isbn', $isbn)
            ->first();

        // Si note_content est "0", on supprime la note si elle existe
        if ($validated['note_content'] === "0")  {
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
