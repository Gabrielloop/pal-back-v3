<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;


class UserController extends Controller
{
    // GET /api/users/
    public function index()
    {
        return response()->json([
            'success' => true,
            'message' => 'Liste des utilisateurs',
            'data' => User::all()
        ],200);
    }

    // GET /api/users/me
    public function me(Request $request)
    {
        return response()->json([
            'success' => true,
            'message' => 'Utilisateur connecté',
            'data' => $request->user()
        ],200);
    }

    // PUT /api/users/me
    public function updateMe(Request $request)
    {
        $user = $request->user();



        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $user->id,
            'password' => 'sometimes|string|min:8|confirmed',
            'is_dark_mode' => 'sometimes|boolean',
        ]);

        if (isset($validated['password'])) {
            $validated['password'] = bcrypt($validated['password']);
        }
        \Log::info('--- updateMe debug ---');
        \Log::info('Raw input', $request->all());
        \Log::info('Validated data', $validated);
        \Log::info('is_dark_mode in validated', [
            'has' => array_key_exists('is_dark_mode', $validated),
            'value' => $validated['is_dark_mode'] ?? null,
        ]);

        $user->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Profil mis à jour',
            'data' => $user->fresh(),
        ],200);
    }

    // POST /api/users
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

    // GET /api/users/{id}
    public function show($id)
    {
        $user = User::findOrFail($id);

        return response()->json([
            'success' => true,
            'message' => 'Utilisateur trouvé',
            'data' => $user,
        ],200);
    }

    // PUT /api/users/update/{id}   ADMIN
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|string|email|max:255|unique:users,email,' . $user->id,
            'is_dark_mode' => 'sometimes|boolean',
        ]);

        $user->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Utilisateur mis à jour',
            'data' => $user,
        ],200);
    }

    // DELETE /api/users/delete/{id}    ADMIN
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'Utilisateur supprimé',
        ],200);
    }
}
