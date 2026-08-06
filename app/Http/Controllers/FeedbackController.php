<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FeedbackController extends Controller
{
    /**
     * Store user feedback (thumbs up / thumbs down) for a prediction.
     *
     * Accepts JSON body: { "is_correct": true|false, "comment": "optional" }
     * Rate-limited to 10 per minute to prevent spam.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'is_correct' => 'required|boolean',
            'comment'    => 'nullable|string|max:500',
        ]);

        Feedback::create([
            'prediction_id' => null, // The frontend doesn't track prediction IDs yet
            'user_id'       => $request->user()?->id,
            'is_correct'    => $validated['is_correct'],
            'comment'       => $validated['comment'] ?? null,
            'ip_address'    => $request->ip(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Thank you for your feedback!',
        ]);
    }
}
