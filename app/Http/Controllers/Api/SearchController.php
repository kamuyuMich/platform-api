<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ContentItemResource;
use App\Models\ContentItem;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    /**
     * GET /api/search?q=interoperability
     * Groups results by type so the frontend can render
     * "Articles / Research / Projects / Resources / Tools" sections.
     */
    public function __invoke(Request $request)
    {
        $validated = $request->validate([
            'q' => 'required|string|min:2|max:120',
        ]);

        $results = ContentItem::search($validated['q'])
            ->query(fn ($query) => $query->published()->with('category'))
            ->take(30)
            ->get()
            ->groupBy('type');

        return response()->json(
            $results->map(fn ($items) => ContentItemResource::collection($items))
        );
    }
}
