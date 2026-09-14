<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ContentItemResource;
use App\Models\ContentItem;
use Illuminate\Http\Request;

class ContentItemController extends Controller
{
    /**
     * GET /api/content-items?type=article&category=ai&tag=ethics&per_page=12
     */
    public function index(Request $request)
    {
        $validated = $request->validate([
            'type' => 'nullable|string|in:' . implode(',', ContentItem::TYPES),
            'category' => 'nullable|string',
            'section' => 'nullable|string',
            'tag' => 'nullable|string',
            'per_page' => 'nullable|integer|min:1|max:50',
        ]);

        $query = ContentItem::query()
            ->published()
            ->with(['category.section', 'author', 'tags'])
            ->latest('published_at');

        if (!empty($validated['type'])) {
            $query->ofType($validated['type']);
        }

        if (!empty($validated['category'])) {
            $query->inCategory($validated['category']);
        }

        if (!empty($validated['section'])) {
            $query->whereHas('category.section', fn ($q) => $q->where('slug', $validated['section']));
        }

        if (!empty($validated['tag'])) {
            $query->whereHas('tags', fn ($q) => $q->where('slug', $validated['tag']));
        }

        $items = $query->paginate($validated['per_page'] ?? 12);

        return ContentItemResource::collection($items);
    }

    /**
     * GET /api/content-items/{slug}
     */
    public function show(string $slug)
    {
        $item = ContentItem::query()
            ->published()
            ->with(['category', 'author', 'tags'])
            ->where('slug', $slug)
            ->firstOrFail();

        return new ContentItemResource($item);
    }

    /**
     * GET /api/content-items/{slug}/related
     */
    public function related(string $slug)
    {
        $item = ContentItem::where('slug', $slug)->firstOrFail();

        $related = ContentItem::query()
            ->published()
            ->where('id', '!=', $item->id)
            ->where('type', $item->type)
            ->when($item->category_id, fn ($q) => $q->where('category_id', $item->category_id))
            ->with(['category'])
            ->latest('published_at')
            ->limit(3)
            ->get();

        return ContentItemResource::collection($related);
    }
}
