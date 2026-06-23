<?php

namespace App\Http\Controllers;

use App\Models\Prediction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HistoryController extends Controller
{
    public function index(Request $request): View
    {
        $userId = $request->user()->id;
        $baseQuery = Prediction::query()->where('user_id', $userId);

        $predictions = (clone $baseQuery)->latest()->paginate(10);

        $stats = [
            'total' => (clone $baseQuery)->count(),
            'real' => (clone $baseQuery)->where('result', 'REAL')->count(),
            'fake' => (clone $baseQuery)->where('result', 'FAKE')->count(),
            'uncertain' => (clone $baseQuery)->where('result', 'UNCERTAIN')->count(),
        ];

        return view('history.index', compact('predictions', 'stats'));
    }

    public function destroy(Prediction $prediction): RedirectResponse
    {
        abort_unless($prediction->user_id === auth()->id(), 403);

        $prediction->delete();

        return redirect()->route('history.index')->with('status', 'History entry deleted.');
    }

    public function recheck(Prediction $prediction): RedirectResponse
    {
        abort_unless($prediction->user_id === auth()->id(), 403);

        return redirect()->route('home', ['text' => $prediction->input_text]);
    }
}
