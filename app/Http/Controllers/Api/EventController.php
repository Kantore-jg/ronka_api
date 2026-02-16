<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventAssignment;
use App\Models\EventComment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        if ($user?->isAdmin()) {
            $events = Event::with(['creator', 'assignments.member', 'comments.user'])->latest()->get();
        } elseif ($user?->isMember()) {
            $eventIds = EventAssignment::where('member_id', $user->id)->pluck('event_id');
            $events = Event::with(['assignments.member', 'comments.user'])
                ->whereIn('id', $eventIds)
                ->orWhereHas('assignments', fn ($q) => $q->where('member_id', $user->id))
                ->latest()
                ->get();
        } else {
            $events = Event::with(['assignments.member', 'comments.user'])->latest()->get();
        }
        return response()->json($events);
    }

    public function store(Request $request): JsonResponse
    {
        if (!$request->user()?->isAdmin()) {
            abort(403, 'Accès réservé à l\'administrateur.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'date' => 'required|date',
            'lieu' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        $event = Event::create([
            ...$validated,
            'created_by' => $request->user()->id,
        ]);

        return response()->json($event, 201);
    }

    public function assignMember(Request $request, Event $event): JsonResponse
    {
        if (!$request->user()?->isAdmin()) {
            abort(403, 'Accès réservé à l\'administrateur.');
        }

        $validated = $request->validate(['member_id' => 'required|exists:users,id']);

        $member = \App\Models\User::findOrFail($validated['member_id']);
        if ($member->role !== 'member') {
            abort(422, 'L\'utilisateur doit être un membre du club.');
        }

        $assignment = EventAssignment::firstOrCreate([
            'event_id' => $event->id,
            'member_id' => $validated['member_id'],
        ]);

        return response()->json($assignment->load('member'));
    }

    public function addComment(Request $request, int $eventId): JsonResponse
    {
        $request->validate(['comment' => 'required|string']);

        $user = $request->user();
        if (!$user) {
            abort(401, 'Authentification requise.');
        }

        $event = Event::findOrFail($eventId);
        $comment = EventComment::create([
            'event_id' => $eventId,
            'comment' => $request->comment,
            'user_id' => $user->id,
        ]);

        return response()->json($comment->load('user'), 201);
    }
}
