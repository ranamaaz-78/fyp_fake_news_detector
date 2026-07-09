<?php

namespace App\Services;

use App\Models\TrainingJob;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;

class MlTrainingService
{
    /**
     * Copy the prepared Fake/True CSVs into the ML raw directory, create a
     * TrainingJob record, and kick off the background training run.
     *
     * On failure the job is marked failed and a RuntimeException is rethrown so
     * the caller can surface the message.
     *
     * @param  array<string, mixed>  $auditMeta
     */
    public function launchJob(
        int $userId,
        string $fakeSourcePath,
        string $trueSourcePath,
        ?string $fakeRef = null,
        ?string $trueRef = null,
        array $auditMeta = [],
    ): TrainingJob {
        $rawDir = base_path('ml/data/raw');
        File::ensureDirectoryExists($rawDir);

        File::copy($fakeSourcePath, $rawDir.'/Fake.csv');
        File::copy($trueSourcePath, $rawDir.'/True.csv');

        $jobId = (string) Str::uuid();

        $job = TrainingJob::create([
            'started_by' => $userId,
            'ml_job_id' => $jobId,
            'status' => 'queued',
            'progress' => 0,
            'stage' => 'Queued',
            'fake_csv_path' => $fakeRef ?? $fakeSourcePath,
            'true_csv_path' => $trueRef ?? $trueSourcePath,
            'started_at' => now(),
        ]);

        try {
            $this->startTraining($jobId);

            $job->update([
                'status' => 'running',
                'stage' => 'Starting training...',
            ]);

            AuditLogger::log('training.started', $userId, TrainingJob::class, $job->id, array_merge([
                'ml_job_id' => $jobId,
            ], $auditMeta));

            return $job;
        } catch (RuntimeException $e) {
            $job->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
                'finished_at' => now(),
            ]);

            AuditLogger::log('training.failed', $userId, TrainingJob::class, $job->id, [
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

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
