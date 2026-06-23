<?php

namespace App\Http\Controllers;

use App\Models\Prediction;
use App\Services\ArticleExtractionService;
use App\Services\FakeNewsApiService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use RuntimeException;

class NewsCheckController extends Controller
{
    public function __construct(
        private FakeNewsApiService $ml,
        private ArticleExtractionService $extractor,
    ) {}

    public function home(Request $request): View
    {
        return view('news.home', [
            'prefill' => $request->query('text', ''),
        ]);
    }

    public function check(Request $request): View|RedirectResponse
    {
        $validated = $request->validate([
            'text' => [
                'required_without:url',
                'nullable',
                'string',
                'min:'.config('fni.min_input_chars'),
                'max:'.config('fni.max_input_chars'),
            ],
            'url' => [
                'required_without:text',
                'nullable',
                'url',
                'max:2048',
                'regex:/^https?:\/\//i',
            ],
        ]);

        if ($request->filled('text') && $request->filled('url')) {
            return back()
                ->withInput()
                ->withErrors(['text' => 'Provide either article text or a URL, not both.']);
        }

        if ($request->filled('url')) {
            try {
                $text = $this->extractor->extract($validated['url']);
            } catch (RuntimeException $e) {
                return back()
                    ->withInput()
                    ->withErrors(['url' => $e->getMessage()]);
            }
        } else {
            $text = $validated['text'];
        }

        try {
            $result = $this->ml->predict($text);
        } catch (RuntimeException $e) {
            $field = $request->filled('url') ? 'url' : 'text';

            return back()
                ->withInput()
                ->withErrors([$field => $e->getMessage()]);
        }

        Prediction::create([
            'user_id' => $request->user()?->id,
            'input_text' => $text,
            'result' => $result['label'],
            'confidence' => $result['confidence'],
            'confidence_level' => $result['confidence_level'] ?? null,
            'model_used' => $result['model'],
        ]);

        $view = match ($result['label']) {
            'REAL' => 'news.result-real',
            'FAKE' => 'news.result-fake',
            default => 'news.result-uncertain',
        };

        return view($view, [
            'text' => $text,
            'result' => $result,
            'statusBar' => match ($result['label']) {
                'REAL' => 'bg-status-real',
                'FAKE' => 'bg-status-fake',
                default => 'bg-status-uncertain',
            },
        ]);
    }
}
