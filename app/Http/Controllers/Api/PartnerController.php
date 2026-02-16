<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Partner;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PartnerController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        if ($user?->isAdmin()) {
            return response()->json(Partner::with('user')->latest()->get());
        }
        return response()->json(Partner::where('status', 'approved')->get());
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'contact' => 'required|string|max:255',
            'email' => 'nullable|email',
            'company' => 'nullable|string|max:255',
            'message' => 'nullable|string',
        ]);

        $partner = Partner::create([
            'name' => $validated['name'],
            'contact' => $validated['contact'],
            'email' => $validated['email'] ?? null,
            'company' => $validated['company'] ?? null,
            'message' => $validated['message'] ?? null,
            'user_id' => $request->user()?->id,
        ]);

        return response()->json($partner, 201);
    }

    public function approve(Request $request, int $id): JsonResponse
    {
        if (!$request->user()?->isAdmin()) {
            abort(403, 'Accès réservé à l\'administrateur.');
        }
        $partner = Partner::findOrFail($id);
        $partner->update(['status' => 'approved']);
        return response()->json($partner);
    }
}
