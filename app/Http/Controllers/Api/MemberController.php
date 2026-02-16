<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class MemberController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        if (!$request->user()?->isAdmin()) {
            abort(403, 'Accès réservé à l\'administrateur.');
        }
        $members = User::where('role', 'member')->get(['id', 'name', 'email', 'username', 'created_at']);
        return response()->json($members);
    }

    public function store(Request $request): JsonResponse
    {
        if (!$request->user()?->isAdmin()) {
            abort(403, 'Accès réservé à l\'administrateur.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username',
            'email' => 'nullable|email',
            'password' => ['nullable', Password::defaults()],
        ]);

        $password = $validated['password'] ?? 'ronka' . substr((string) time(), -4);
        $email = $validated['email'] ?? $validated['username'] . '@ronka.local';

        $member = User::create([
            'name' => $validated['name'],
            'username' => $validated['username'],
            'email' => $email,
            'password' => Hash::make($password),
            'role' => 'member',
        ]);

        return response()->json([
            'member' => [
                'id' => $member->id,
                'name' => $member->name,
                'username' => $member->username,
                'email' => $member->email,
            ],
            'password' => $password,
        ], 201);
    }

    public function destroy(Request $request, User $member): JsonResponse
    {
        if (!$request->user()?->isAdmin()) {
            abort(403, 'Accès réservé à l\'administrateur.');
        }
        if ($member->role !== 'member') {
            abort(403, 'Seuls les membres peuvent être supprimés.');
        }
        $member->delete();
        return response()->json(['message' => 'Membre supprimé']);
    }
}
