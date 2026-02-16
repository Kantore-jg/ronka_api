<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $this->authorizeAdmin($request);
        $bookings = Booking::with('user')->latest()->get();
        return response()->json($bookings);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'contact' => 'required|string|max:255',
            'eventType' => 'required|string|max:255',
            'date' => 'required|date',
            'lieu' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $booking = Booking::create([
            'name' => $validated['name'],
            'contact' => $validated['contact'],
            'event_type' => $validated['eventType'],
            'date' => $validated['date'],
            'lieu' => $validated['lieu'],
            'description' => $validated['description'] ?? null,
            'user_id' => $request->user()?->id,
        ]);

        return response()->json($booking, 201);
    }

    private function authorizeAdmin(Request $request): void
    {
        if (!$request->user()?->isAdmin()) {
            abort(403, 'Accès réservé à l\'administrateur.');
        }
    }
}
