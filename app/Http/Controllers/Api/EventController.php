<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\EventAssignmentNotification;
use App\Models\Event;
use App\Models\EventAssignment;
use App\Models\EventComment;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class EventController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        if ($user?->isAdmin()) {
            $events = Event::with(['creator', 'assignments.member', 'comments.user'])->latest()->get();
        } elseif ($user?->isMember()) {
            $events = Event::with(['assignments.member', 'comments.user'])
                ->whereHas('assignments', fn ($q) => $q->where('member_id', $user->id))
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

        $member = User::findOrFail($validated['member_id']);
        if ($member->role !== 'member') {
            abort(422, 'L\'utilisateur doit être un membre du club.');
        }

        $assignment = EventAssignment::firstOrCreate(
            [
                'event_id' => $event->id,
                'member_id' => $validated['member_id'],
            ],
            ['status' => 'pending']
        );

        if (!$assignment->notified) {
            $frontendUrl = rtrim(env('FRONTEND_URL', 'http://localhost:5173'), '/');
            $confirmUrl = "{$frontendUrl}/member/assignments/{$assignment->id}/confirm";
            $declineUrl = "{$frontendUrl}/member/assignments/{$assignment->id}/decline";

            try {
                Mail::to($member->email)->send(
                    new EventAssignmentNotification($member, $event, $confirmUrl, $declineUrl)
                );
                $assignment->update(['notified' => true]);
            } catch (\Throwable $e) {
                report($e);
            }
        }

        return response()->json($assignment->load('member'));
    }

    public function confirmAssignment(Request $request, int $assignmentId): JsonResponse
    {
        $user = $request->user();
        $assignment = EventAssignment::with('event')->findOrFail($assignmentId);

        if ($assignment->member_id !== $user->id && !$user->isAdmin()) {
            abort(403, 'Vous ne pouvez confirmer que vos propres assignations.');
        }

        $validated = $request->validate([
            'status' => 'required|in:confirmed,declined',
        ]);

        $assignment->update([
            'status' => $validated['status'],
            'confirmed_at' => $validated['status'] === 'confirmed' ? now() : null,
        ]);

        return response()->json([
            'message' => $validated['status'] === 'confirmed'
                ? 'Présence confirmée avec succès.'
                : 'Vous avez décliné l\'assignation.',
            'assignment' => $assignment->load(['event', 'member']),
        ]);
    }

    public function getConfirmations(Request $request, Event $event): JsonResponse
    {
        if (!$request->user()?->isAdmin()) {
            abort(403, 'Accès réservé à l\'administrateur.');
        }

        $assignments = $event->assignments()->with('member')->get();

        $summary = [
            'event' => $event,
            'total' => $assignments->count(),
            'confirmed' => $assignments->where('status', 'confirmed')->count(),
            'declined' => $assignments->where('status', 'declined')->count(),
            'pending' => $assignments->where('status', 'pending')->count(),
            'all_confirmed' => $assignments->count() > 0 && $assignments->every(fn ($a) => $a->status === 'confirmed'),
            'members' => $assignments->map(fn ($a) => [
                'id' => $a->id,
                'member' => $a->member,
                'status' => $a->status,
                'confirmed_at' => $a->confirmed_at,
                'notified' => $a->notified,
            ]),
        ];

        return response()->json($summary);
    }

    public function myAssignments(Request $request): JsonResponse
    {
        $user = $request->user();

        $assignments = EventAssignment::with('event')
            ->where('member_id', $user->id)
            ->latest()
            ->get();

        return response()->json($assignments);
    }

    public function addComment(Request $request, int $eventId): JsonResponse
    {
        $request->validate(['comment' => 'required|string']);

        $user = $request->user();
        if (!$user) {
            abort(401, 'Authentification requise.');
        }

        Event::findOrFail($eventId);
        $comment = EventComment::create([
            'event_id' => $eventId,
            'comment' => $request->comment,
            'user_id' => $user->id,
        ]);

        return response()->json($comment->load('user'), 201);
    }
}
