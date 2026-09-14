<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Section;

class SectionController extends Controller
{
    public function index()
    {
        return Section::query()
            ->orderBy('order')
            ->get(['id', 'name', 'slug', 'color']);
    }
}