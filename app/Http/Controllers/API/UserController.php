<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;


class UserController extends Controller
{

    public function index()
    {
        return response()->json([
            'success' => true,
            'message' => 'Liste des utilisateurs',
            'data' => User::all()
        ]);
    }

    public function me(Request $request)
    {
        return response()->json([
            'success' => true,
            'message' => 'Utilisateur connecté',
            'data' => $request->user()
        ]);
    }

    public function updateMe(Request $request)
{
    $user = $request->user();

    $validated = $request->validate([
        'name' => 'sometimes|string|max:255',
        'email' => 'sometimes|email|unique:users,email,' . $user->id,
        'password' => 'sometimes|string|min:8|confirmed',
    ]);

    if (isset($validated['password'])) {
        $validated['password'] = bcrypt($validated['password']);
    }

    $user->update($validated);

    return response()->json([
        'success' => true,
        'message' => 'Profil mis à jour',
        'data' => $user,
    ]);
}


    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'role' => 'user',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Utilisateur créé avec succès',
            'data' => $user,
        ], 201);
    }

    public function show($id)
    {
        $user = User::findOrFail($id);

        return response()->json([
            'success' => true,
            'message' => 'Utilisateur trouvé',
            'data' => $user,
        ]);
    }


    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|string|email|max:255|unique:users,email,' . $user->id,
        ]);

        $user->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Utilisateur mis à jour',
            'data' => $user,
        ]);
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'Utilisateur supprimé',
        ]);
    }
}
