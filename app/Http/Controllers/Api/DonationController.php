<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Donation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DonationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $this->authorizeAdmin($request);
        $donations = Donation::with('user')->latest()->get();
        return response()->json($donations);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'contact' => 'required|string|max:255',
            'amount' => 'required|string|max:100',
            'method' => 'required|string|max:50',
            'paymentDetails' => 'nullable|string',
            'message' => 'nullable|string',
        ]);

        $donation = Donation::create([
            'name' => $validated['name'],
            'contact' => $validated['contact'],
            'amount' => $validated['amount'],
            'method' => $validated['method'],
            'payment_details' => $validated['paymentDetails'] ?? null,
            'message' => $validated['message'] ?? null,
            'user_id' => $request->user()?->id,
        ]);

        return response()->json($donation, 201);
    }

    private function authorizeAdmin(Request $request): void
    {
        if (!$request->user()?->isAdmin()) {
            abort(403, 'Accès réservé à l\'administrateur.');
        }
    }
}
