<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\NewContactMessage;
use App\Models\ContactMessage;
use App\Models\SiteProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'organization' => 'nullable|string|max:255',
            'reason' => 'required|in:collaboration,consulting,speaking,research,partnership,media,general',
            'message' => 'required|string|max:5000',
        ]);

        $contactMessage = ContactMessage::create($validated);

        $notifyEmail = SiteProfile::current()->email;
        if ($notifyEmail) {
            Mail::to($notifyEmail)->send(new NewContactMessage($contactMessage));
        }

        return response()->json([
            'message' => 'Thanks for reaching out - we will get back to you soon.',
        ], 201);
    }
}
