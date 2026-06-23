<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class MlTrainingService
{
    public function startTraining(string $jobId): array
    {
        $baseUrl = rtrim(config('fni.ml_api_url'), '/');

        try {
            $response = Http::timeout(config('fni.ml_train_timeout'))
                ->acceptJson()
                ->post("{$baseUrl}/train/start", ['job_id' => $jobId]);

            if ($response->status() === 409) {
                throw new RuntimeException('A training job is already running on the ML service.');
            }

            if ($response->status() === 422) {
                throw new RuntimeException($response->json('message') ?? 'Training dataset files are missing.');
            }

            if ($response->failed()) {
                throw new RuntimeException($response->json('message') ?? 'Could not start training.');
            }

            return $response->json();
        } catch (ConnectionException) {
            throw new RuntimeException('ML service is unavailable. Start the Flask API first.');
        }
    }

    public function getStatus(): array
    {
        $baseUrl = rtrim(config('fni.ml_api_url'), '/');

        try {
            $response = Http::timeout(config('fni.ml_timeout'))
                ->acceptJson()
                ->get("{$baseUrl}/train/status");

            if ($response->failed()) {
                throw new RuntimeException('Could not fetch training status.');
            }

            return $response->json();
        } catch (ConnectionException) {
            throw new RuntimeException('ML service is unavailable.');
        }
    }

    public function reloadModel(): bool
    {
        $baseUrl = rtrim(config('fni.ml_api_url'), '/');

        try {
            return Http::timeout(config('fni.ml_timeout'))
                ->acceptJson()
                ->post("{$baseUrl}/train/reload")
                ->successful();
        } catch (\Throwable) {
            return false;
        }
    }
}
