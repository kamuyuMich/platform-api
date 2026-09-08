<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    /**
     * POST /api/contact
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'organization' => 'nullable|string|max:255',
            'reason' => 'required|in:collaboration,consulting,speaking,research,partnership,media,general',
            'message' => 'required|string|max:5000',
        ]);

        ContactMessage::create($validated);

        return response()->json([
            'message' => 'Thanks for reaching out - we will get back to you soon.',
        ], 201);
    }
}
