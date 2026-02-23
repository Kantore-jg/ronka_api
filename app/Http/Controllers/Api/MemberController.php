<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\MemberWelcome;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class MemberController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        if (!$request->user()?->isAdmin()) {
            abort(403, 'Accès réservé à l\'administrateur.');
        }
        $members = User::where('role', 'member')
            ->orderBy('created_at', 'desc')
            ->get(['id', 'name', 'email', 'username', 'created_at']);
        return response()->json($members);
    }

    private function generateMemberCode(): string
    {
        $year = date('Y');
        $prefix = "Ronka-{$year}-";

        $lastMember = User::where('role', 'member')
            ->where('username', 'like', $prefix . '%')
            ->orderByRaw("CAST(SUBSTRING(username, ?) AS UNSIGNED) DESC", [strlen($prefix) + 1])
            ->first();

        if ($lastMember) {
            $lastNumber = (int) substr($lastMember->username, strlen($prefix));
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        return $prefix . str_pad((string) $nextNumber, 4, '0', STR_PAD_LEFT);
    }

    private function generatePassword(): string
    {
        return 'Ronka' . Str::random(6);
    }

    public function store(Request $request): JsonResponse
    {
        if (!$request->user()?->isAdmin()) {
            abort(403, 'Accès réservé à l\'administrateur.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
        ]);

        $code = $this->generateMemberCode();
        $password = $this->generatePassword();

        $member = User::create([
            'name' => $validated['name'],
            'username' => $code,
            'email' => $validated['email'],
            'password' => Hash::make($password),
            'role' => 'member',
        ]);

        $loginUrl = rtrim(env('FRONTEND_URL', 'http://localhost:5173'), '/') . '/auth/login';

        try {
            Mail::to($member->email)->send(new MemberWelcome($member, $password, $loginUrl));
        } catch (\Throwable $e) {
            report($e);
        }

        return response()->json([
            'member' => [
                'id' => $member->id,
                'name' => $member->name,
                'username' => $member->username,
                'email' => $member->email,
            ],
            'password' => $password,
            'code' => $code,
            'email_sent' => true,
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
