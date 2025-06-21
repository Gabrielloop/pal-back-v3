<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use Illuminate\Http\Request;
use App\Services\BookCacheService;

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

    // DELETE /api/comments/userid/{userid}/{isbn}/   ADMIN
    public function destroyByUserIdAndIsbn($userid, $isbn)
    {
        $comment = Comment::where('user_id', $userid)
                        ->where('isbn', $isbn)
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
    

    // PUT /api/com/api/comments/userid/{userid}/{isbn}/   ADMIN
    public function updateByUserIdAndIsbn(Request $request, $userid, $isbn)
    {
        $comment = Comment::where('user_id', $userid)
                        ->where('isbn', $isbn)
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

    // GET /api/comments/isbn/{isbn}  USER
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

    // GET /api/comments/  USER
     public function getCommentForUser(Request $request)
    {
        $userId = $request->user()->id;
        $comments = Comment::where('user_id', $userId)->get();
        return response()->json([
            'success' => true,
            'message' => 'Commentaires de l’utilisateur',
            'data' => $comments,
        ], 200);
    }
    

    // POST /api/comments/   USER
        public function addOrUpdateComment(Request $request)
        {

            $isbn = $request->input('isbn');
            $book = BookCacheService::ensurePersisted($isbn);

            if (!$book) {
                return response()->json([
                    'success' => false,
                    'message' => 'Livre introuvable dans le cache.',
                ], 404);
            }


            $validated = $request->validate([
                'isbn' => 'required|string|exists:books,isbn',
                'comment_content' => 'nullable|string|max:255',
            ]);

            $userId = $request->user()->id;

            $comment = Comment::where('isbn', $validated['isbn'])
                ->where('user_id', $userId)
                ->first();

            if (empty(trim($validated['comment_content']))) {
                if ($comment) {
                    $comment->delete();
                    return response()->json([
                        'success' => true,
                        'message' => 'Commentaire supprimé',
                    ]);
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'Aucun commentaire à supprimer',
                    ], 404);
                }
            }

            if ($comment) {
                $comment->update([
                    'comment_content' => $validated['comment_content'],
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Commentaire mis à jour',
                    'data' => $comment,
                ]);
            }

            $comment = Comment::create([
                'isbn' => $validated['isbn'],
                'user_id' => $userId,
                'comment_content' => $validated['comment_content'],
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Commentaire ajouté',
                'data' => $comment,
            ], 201);
        }

    
    // DELETE /api/comments/isbn/{isbn}   USER
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
