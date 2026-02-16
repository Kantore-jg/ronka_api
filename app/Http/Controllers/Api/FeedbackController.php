<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\FeedbackReceived;
use App\Models\Feedback;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class FeedbackController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        if (!$request->user()?->isAdmin()) {
            abort(403, 'Accès réservé à l\'administrateur.');
        }
        $feedbacks = Feedback::with('user')->latest()->get();
        return response()->json($feedbacks);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'type' => 'required|in:feedback,suggestion',
            'name' => 'nullable|string|max:255',
            'contact' => 'nullable|string|max:255',
            'message' => 'required|string',
        ]);

        $senderEmail = $request->user()?->email ?? $validated['contact'] ?? null;

        $feedback = Feedback::create([
            'type' => $validated['type'],
            'name' => $validated['name'] ?? null,
            'contact' => $validated['contact'] ?? null,
            'message' => $validated['message'],
            'sender_email' => $senderEmail,
            'user_id' => $request->user()?->id,
        ]);

        try {
            Mail::to('codewithkantox@gmail.com')->send(new FeedbackReceived($feedback));
        } catch (\Throwable $e) {
            report($e);
        }

        return response()->json($feedback, 201);
    }
}
