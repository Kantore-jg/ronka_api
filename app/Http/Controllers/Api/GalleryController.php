<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\GalleryItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GalleryController extends Controller
{
    public function index(): JsonResponse
    {
        $items = GalleryItem::orderBy('created_at', 'desc')->get();
        return response()->json($items);
    }

    public function store(Request $request): JsonResponse
    {
        if (!$request->user()?->isAdmin()) {
            abort(403, 'Accès réservé à l\'administrateur.');
        }

        $validated = $request->validate([
            'type' => 'required|in:image,video',
            'url' => 'required|url',
            'title' => 'nullable|string|max:255',
        ]);

        $item = GalleryItem::create($validated);
        return response()->json($item, 201);
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        if (!$request->user()?->isAdmin()) {
            abort(403, 'Accès réservé à l\'administrateur.');
        }
        GalleryItem::findOrFail($id)->delete();
        return response()->json(['message' => 'Élément supprimé']);
    }
}
