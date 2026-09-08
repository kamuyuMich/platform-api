<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\NewsletterSubscriber;
use Illuminate\Http\Request;

class NewsletterController extends Controller
{
    /**
     * POST /api/newsletter/subscribe
     */
    public function subscribe(Request $request)
    {
        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'email' => 'required|email|max:255',
        ]);

        $subscriber = NewsletterSubscriber::firstOrNew(['email' => $validated['email']]);

        $subscriber->name = $validated['name'] ?? $subscriber->name;
        $subscriber->subscribed_at = now();
        $subscriber->unsubscribed_at = null;
        $subscriber->save();

        return response()->json([
            'message' => 'Subscribed successfully.',
        ], 201);
    }

    /**
     * POST /api/newsletter/unsubscribe
     */
    public function unsubscribe(Request $request)
    {
        $validated = $request->validate([
            'token' => 'required|string',
        ]);

        $subscriber = NewsletterSubscriber::where('unsubscribe_token', $validated['token'])
            ->firstOrFail();

        $subscriber->update(['unsubscribed_at' => now()]);

        return response()->json(['message' => 'Unsubscribed.']);
    }
}
