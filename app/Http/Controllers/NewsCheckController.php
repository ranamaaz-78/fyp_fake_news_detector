<?php

namespace App\Http\Controllers;

use App\Models\Prediction;
use App\Services\ArticleExtractionService;
use App\Services\FakeNewsApiService;
use App\Services\OcrService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use RuntimeException;

class NewsCheckController extends Controller
{
    public function __construct(
        private FakeNewsApiService $ml,
        private ArticleExtractionService $extractor,
        private OcrService $ocr,
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
                'required_without_all:url,image',
                'nullable',
                'string',
                'min:'.config('fni.min_input_chars'),
                'max:'.config('fni.max_input_chars'),
            ],
            'url' => [
                'required_without_all:text,image',
                'nullable',
                'url',
                'max:2048',
                'regex:/^https?:\/\//i',
            ],
            'image' => [
                'required_without_all:text,url',
                'nullable',
                'image',
                'mimes:'.config('fni.image_mimes'),
                'max:'.config('fni.image_max_kb'),
            ],
        ]);

        $inputs = [
            'text' => $request->filled('text'),
            'url' => $request->filled('url'),
            'image' => $request->hasFile('image'),
        ];

        if (count(array_filter($inputs)) > 1) {
            return back()
                ->withInput()
                ->withErrors(['text' => 'Provide only one input: article text, a URL, or an image.']);
        }

        $inputField = 'text';

        try {
            if ($request->hasFile('image')) {
                $inputField = 'image';
                $text = $this->ocr->extract($request->file('image'));
            } elseif ($request->filled('url')) {
                $inputField = 'url';
                $text = $this->extractor->extract($validated['url']);
            } else {
                $text = $validated['text'];
            }
        } catch (RuntimeException $e) {
            return back()
                ->withInput()
                ->withErrors([$inputField => $e->getMessage()]);
        }

        try {
            $result = $this->ml->predict($text);
        } catch (RuntimeException $e) {
            return back()
                ->withInput()
                ->withErrors([$inputField => $e->getMessage()]);
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
