<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class FakeNewsApiService
{
    public function predict(string $text): array
    {
        $baseUrl = rtrim(config('fni.ml_api_url'), '/');

        try {
            $response = Http::timeout(config('fni.ml_timeout'))
                ->acceptJson()
                ->post("{$baseUrl}/predict", ['text' => $text]);

            if ($response->status() === 422) {
                $message = $response->json('message') ?? 'Invalid input.';
                throw new RuntimeException($message);
            }

            if ($response->failed()) {
                $message = $response->json('message') ?? 'ML service error.';
                throw new RuntimeException($message);
            }

            return $response->json();
        } catch (ConnectionException) {
            throw new RuntimeException('Service temporarily unavailable. Please try again later.');
        } catch (RequestException $e) {
            throw new RuntimeException('Service temporarily unavailable. Please try again later.');
        }
    }

    public function isHealthy(): bool
    {
        try {
            $baseUrl = rtrim(config('fni.ml_api_url'), '/');

            return Http::timeout(3)->get("{$baseUrl}/health")->successful();
        } catch (\Throwable) {
            return false;
        }
    }
}
