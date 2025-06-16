<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Userlist;
use Illuminate\Http\Request;

class UserlistController extends Controller
{
    // GET /api/userlists/all   (ADMIN)
    public function index()
    {
        return response()->json([
            'success' => true,
            'message' => 'Liste de toutes les listes utilisateurs',
            'data' => Userlist::with('books')->get()
        ], 200);
    }

    // DELETE /api/userlists/id/{id}   (ADMIN)
    public function destroyById($id)
    {
        $userlist = Userlist::find($id);

        if (!$userlist) {
            return response()->json([
                'success' => false,
                'message' => 'Liste non trouvée',
            ], 404);
        }

        $userlist->delete();

        return response()->json([
            'success' => true,
            'message' => 'Liste supprimée',
            'data' => $userlist,
        ], 200);
    }

    // PUT /api/userlists/id/{id}   (ADMIN)
    public function updateById(Request $request, $id)
    {
        $userlist = Userlist::findOrFail($id);

        $validated = $request->validate([
            'userlist_name' => 'required|string|max:255',
            'userlist_description' => 'nullable|string',
            'userlist_type' => 'required|string',
        ]);

        $userlist->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Liste mise à jour',
            'data' => $userlist,
        ]);
    }

    // GET /api/userlists   (USER)
    public function getUserLists(Request $request)
    {
        $userId = $request->user()->id;

        $lists = Userlist::with('books')->where('user_id', $userId)->get();

        return response()->json([
            'success' => true,
            'message' => 'Listes de l’utilisateur',
            'data' => $lists,
        ]);
    }
    
    // GET /api/userlists/id/{id}   (USER)
    public function show($id)
    {
        $userlist = Userlist::with('books')->find($id);

        if (!$userlist) {
            return response()->json([
                'success' => false,
                'message' => 'Liste non trouvée',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Liste récupérée',
            'data' => $userlist,
        ]);
    }


    // GET /api/userlists/title/{title}   (USER)
    public function getByTitle(Request $request, $title)
    {
        $userId = $request->user()->id;

        $lists = Userlist::with('books')
            ->where('user_id', $userId)
            ->where('userlist_name', 'like', "%$title%")
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Listes trouvées par titre',
            'data' => $lists,
        ]);
    }

    // POST /api/userlists   (USER)
    public function store(Request $request)
    {
        $validated = $request->validate([
            'userlist_name' => 'required|string|max:255',
            'userlist_description' => 'nullable|string',
            'userlist_type' => 'required|string',
        ]);

        $userlist = Userlist::create([
            'user_id' => $request->user()->id,
            ...$validated
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Liste créée',
            'data' => $userlist,
        ], 201);
    }

    // PUT /api/userlists/id/{id}   (USER)
    public function update(Request $request, $id)
    {
        $userlist = Userlist::where('user_id', $request->user()->id)->findOrFail($id);

        $validated = $request->validate([
            'userlist_name' => 'sometimes|string|max:255',
            'userlist_description' => 'nullable|string',
            'userlist_type' => 'sometimes|string',
        ]);

        $userlist->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Liste mise à jour',
            'data' => $userlist,
        ]);
    }

    // DELETE /api/userlists/id/{id}   (USER)
    public function destroy(Request $request, $id)
    {
        $userlist = Userlist::where('user_id', $request->user()->id)->find($id);

        if (!$userlist) {
            return response()->json([
                'success' => false,
                'message' => 'Liste non trouvée',
            ], 404);
        }

        $userlist->delete();

        return response()->json([
            'success' => true,
            'message' => 'Liste supprimée',
            'data' => $userlist,
        ]);
    }
}
