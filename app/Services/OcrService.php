<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class OcrService
{
    public function extract(UploadedFile $file): string
    {
        $baseUrl = rtrim(config('fni.ml_api_url'), '/');
        $path = $file->getRealPath();

        if ($path === false || ! is_file($path)) {
            throw new RuntimeException('Could not read the uploaded image.');
        }

        try {
            $response = Http::timeout(config('fni.ocr_timeout'))
                ->acceptJson()
                ->attach('image', file_get_contents($path), $file->getClientOriginalName() ?: 'upload')
                ->post("{$baseUrl}/ocr");
        } catch (ConnectionException) {
            throw new RuntimeException('Service temporarily unavailable. Please try again later.');
        }

        if ($response->status() === 422) {
            throw new RuntimeException($response->json('message') ?? 'Could not read text from that image.');
        }

        if ($response->failed()) {
            throw new RuntimeException($response->json('message') ?? 'Could not process that image.');
        }

        $text = trim((string) $response->json('text', ''));

        if ($text === '') {
            throw new RuntimeException('Could not extract enough text from that image.');
        }

        $max = config('fni.max_input_chars');

        if (strlen($text) > $max) {
            $text = substr($text, 0, $max);
        }

        return $text;
    }
}
