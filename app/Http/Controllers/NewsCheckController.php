<?php

namespace App\Http\Controllers;

use App\Models\Prediction;
use App\Services\ArticleExtractionService;
use App\Services\FakeNewsApiService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
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

    public function check(Request $request): View|RedirectResponse|\Illuminate\Http\JsonResponse
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
                'max:4096',
            ],
        ]);

        $inputsCount = 0;
        if ($request->filled('text')) $inputsCount++;
        if ($request->filled('url')) $inputsCount++;
        if ($request->hasFile('image')) $inputsCount++;

        if ($inputsCount > 1) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['error' => 'Provide only one check method: text, url, or image.'], 422);
            }
            return back()
                ->withInput()
                ->withErrors(['text' => 'Provide only one check method: text, url, or image.']);
        }

        if ($request->hasFile('image')) {
            try {
                $imageFile = $request->file('image');
                $imageBytes = file_get_contents($imageFile->getRealPath());

                // --- AI Image Detection (always runs) ---
                $imageAnalysis = null;
                try {
                    $mlBaseUrl = rtrim(config('fni.ml_api_url'), '/');
                    $imgResponse = Http::timeout(config('fni.ml_timeout'))
                        ->attach(
                            'image',
                            $imageBytes,
                            $imageFile->getClientOriginalName()
                        )
                        ->post("{$mlBaseUrl}/analyze-image");

                    if ($imgResponse->successful()) {
                        $imageAnalysis = $imgResponse->json();
                    }
                } catch (\Throwable) {
                    // Image detection is optional — OCR + text analysis still runs.
                }

                // --- OCR Text Extraction ---
                $ocrResponse = Http::attach(
                    'file',
                    $imageBytes,
                    $imageFile->getClientOriginalName()
                )->post('https://api.ocr.space/parse/image', [
                    'apikey' => 'helloworld',
                    'language' => 'eng',
                ]);

                if (!$ocrResponse->successful()) {
                    throw new \RuntimeException('OCR service request failed.');
                }

                $ocrData = $ocrResponse->json();
                if (isset($ocrData['ParsedResults'][0]['ParsedText'])) {
                    $text = trim($ocrData['ParsedResults'][0]['ParsedText']);
                } else {
                    $errorMsg = $ocrData['ErrorMessage'][0] ?? 'Failed to extract text from the image.';
                    throw new \RuntimeException($errorMsg);
                }

                if (strlen($text) < config('fni.min_input_chars')) {
                    // If OCR text is too short but we have image analysis, return just that.
                    if ($imageAnalysis) {
                        if ($request->expectsJson() || $request->ajax()) {
                            return response()->json([
                                'success' => true,
                                'text' => '',
                                'result' => [
                                    'label' => 'UNCERTAIN',
                                    'confidence' => 0,
                                    'confidence_level' => 'LOW',
                                    'model' => 'none',
                                ],
                                'image_analysis' => $imageAnalysis,
                            ]);
                        }
                    }
                    throw new \RuntimeException('The text extracted from the image is too short for analysis.');
                }
            } catch (\RuntimeException $e) {
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json(['error' => $e->getMessage()], 422);
                }
                return back()
                    ->withInput()
                    ->withErrors(['image' => $e->getMessage()]);
            }

        } elseif ($request->filled('url')) {
            try {
                $text = $this->extractor->extract($validated['url']);
            } catch (RuntimeException $e) {
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json(['error' => $e->getMessage()], 422);
                }
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
            $field = $request->hasFile('image') ? 'image' : ($request->filled('url') ? 'url' : 'text');
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['error' => $e->getMessage()], 503);
            }

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

        if ($request->expectsJson() || $request->ajax()) {
            $response = [
                'success' => true,
                'text' => $text,
                'result' => $result,
            ];
            if (isset($imageAnalysis)) {
                $response['image_analysis'] = $imageAnalysis;
            }
            return response()->json($response);
        }

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
