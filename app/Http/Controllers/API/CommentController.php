<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    // GET /api/comments/all    ADMIN
    public function index()
    {
        return response()->json([
            'success' => true,
            'message' => 'Liste des commentaires',
            'data' => Comment::all()
        ], 200);
    }

    // GET /api/comments/{isbn}  USER
    public function getByIsbnForCurrentUser(Request $request, $isbn)
    {
        $userId = $request->user()->id;

        $comment = Comment::where('isbn', $isbn)
                        ->where('user_id', $userId)
                        ->first();

        if (!$comment) {
            return response()->json([
                'success' => false,
                'message' => 'Commentaire non trouvé',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Commentaire trouvé',
            'data' => $comment,
        ]);
    }

    // GET /api/comments/content/{content}  ADMIN
    public function getByContent($content)
    {
        $comments = Comment::where('comment_content', 'like', "%$content%")->get();

        return response()->json([
            'success' => true,
            'message' => 'Commentaires filtrés par contenu',
            'data' => $comments,
        ]);
    }

    // POST /api/comments/{isbn}    USER
    public function store(Request $request)
    {
        $validated = $request->validate([
            'isbn' => 'required|string|exists:books,isbn',
            'comment_content' => 'required|string',
        ]);

        $comment = Comment::create([
            'isbn' => $validated['isbn'],
            'user_id' => $request->user()->id,
            'comment_content' => $validated['comment_content'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Commentaire ajouté',
            'data' => $comment,
        ], 201);
    }

    // PUT /api/comments/{isbn}     USER
    public function update(Request $request, $isbn)
    {
        $userId = $request->user()->id;

        $comment = Comment::where('isbn', $isbn)
                        ->where('user_id', $userId)
                        ->firstOrFail();

        $validated = $request->validate([
            'comment_content' => 'required|string',
        ]);

        $comment->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Commentaire mis à jour',
            'data' => $comment,
        ]);
    }

    // DELETE /api/comments/{isbn}   USER
    public function destroy($isbn, Request $request)
    {
        $userId = $request->user()->id;

        $comment = Comment::where('isbn', $isbn)
                        ->where('user_id', $userId)
                        ->first();

        if (!$comment) {
            return response()->json([
                'success' => false,
                'message' => 'Commentaire non trouvé',
            ], 404);
        }

        $comment->delete();

        return response()->json([
            'success' => true,
            'message' => 'Commentaire supprimé',
            'data' => $comment,
        ]);
    }


    public function error()
    {
        return response()->json([
            'success' => false,
            'message' => 'Erreur serveur simulée',
        ], 500);
    }
}
