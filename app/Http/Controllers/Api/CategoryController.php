<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * GET /api/categories?section=digital_health
     */
    public function index(Request $request)
    {
        $query = Category::query()->withCount('contentItems');

        if ($request->filled('section')) {
            $query->where('section', $request->string('section'));
        }

        return $query->orderBy('name')->get([
            'id', 'name', 'slug', 'section', 'description',
        ]);
    }
}
