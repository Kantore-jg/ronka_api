<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\GalleryItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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

        $type = $request->input('type', 'image');

        if ($type === 'image') {
            $request->validate([
                'type' => 'required|in:image',
                'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
                'title' => 'nullable|string|max:255',
            ]);

            $path = $request->file('image')->store('gallery', 'public');
            $url = asset('storage/' . $path);

            $item = GalleryItem::create([
                'type' => 'image',
                'url' => $url,
                'title' => $request->input('title'),
            ]);
        } else {
            $request->validate([
                'type' => 'required|in:video',
                'url' => 'required|url',
                'title' => 'nullable|string|max:255',
            ]);

            $item = GalleryItem::create([
                'type' => 'video',
                'url' => $request->input('url'),
                'title' => $request->input('title'),
            ]);
        }

        return response()->json($item, 201);
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        if (!$request->user()?->isAdmin()) {
            abort(403, 'Accès réservé à l\'administrateur.');
        }
        $item = GalleryItem::findOrFail($id);
        $path = parse_url($item->url ?? '', PHP_URL_PATH);
        if ($path && str_contains($path, '/storage/')) {
            $relPath = preg_replace('#^/storage/#', '', $path);
            Storage::disk('public')->delete($relPath);
        }
        $item->delete();
        return response()->json(['message' => 'Élément supprimé']);
    }
}
