<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Userlist;
use Illuminate\Http\Request;

class UserlistController extends Controller
{
    public function index()
    {
        return response()->json([
            'success' => true,
            'message' => 'Liste de toutes les listes utilisateurs',
            'data' => Userlist::with('books')->get()
        ], 200);
    }

    public function deleteUserlistByUserId($userlistId)
    {

        $userId = $request->user()->id;

        $userlist = Userlist::where('userlist_id', $userlistId)
                            ->where('user_id', $userId)
                            ->first();

                            
        $userlist = Userlist::find($userlistId);

        if (!$userlist) {
            return response()->json([
                'success' => false,
                'message' => 'Liste non trouvée pour cet utilisateur',
            ], 404);
        }

        $userlist->delete();

        return response()->json([
            'success' => true,
            'message' => 'Liste supprimée avec succès',
            'data' => $userlist,
        ]);
    }
    
    public function updateUserlistByUserId(Request $request, $userlistId)
    {
        $userId = $request->user()->id;

        $userlist = Userlist::where('userlist_id', $userlistId)
                            ->where('user_id', $userId)
                            ->first();
     
        $userlist = Userlist::find($userlistId);

        if (!$userlist) {
            return response()->json([
                'success' => false,
                'message' => 'Liste non trouvée pour cet utilisateur',
            ], 404);
        }

        $validated = $request->validate([
            'userlist_name' => 'sometimes|string|max:255',
            'userlist_description' => 'nullable|string',
            'userlist_type' => 'sometimes|string',
        ]);

        $userlist->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Liste mise à jour avec succès',
            'data' => $userlist,
        ]);
    }

    public function collection(Request $request)
    {
        $userId = $request->user()->id;

        $lists = Userlist::with('books')->where('user_id', $userId)->get();

        return response()->json([
            'success' => true,
            'message' => 'Listes de l’utilisateur',
            'data' => $lists,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
        'userlist_name' => 'required|string|min:3|max:100',
        'userlist_description' => 'nullable|string|max:255',
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
