<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TrainingJob;
use App\Services\MlTrainingService;
use App\Support\CsvHelper;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use RuntimeException;

class TrainingController extends Controller
{
    public function __construct(private MlTrainingService $training) {}

    public function index(): View
    {
        $activeJob = TrainingJob::query()
            ->whereIn('status', ['queued', 'running'])
            ->latest()
            ->first();

        $history = TrainingJob::with('starter')
            ->latest()
            ->limit(10)
            ->get();

        $uploadToken = session('training_upload_token');

        return view('admin.training', compact('activeJob', 'history', 'uploadToken'));
    }

    public function upload(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'fake_csv' => ['required', 'file', 'mimes:csv,txt', 'max:102400'],
            'true_csv' => ['required', 'file', 'mimes:csv,txt', 'max:102400'],
        ]);

        $this->assertKaggleFilename($validated['fake_csv'], 'Fake.csv');
        $this->assertKaggleFilename($validated['true_csv'], 'True.csv');
        $this->assertMinimumRows($validated['fake_csv'], 'fake_csv', 100);
        $this->assertMinimumRows($validated['true_csv'], 'true_csv', 100);

        $token = (string) Str::uuid();
        $directory = "training/{$token}";

        $fakePath = $validated['fake_csv']->storeAs($directory, 'Fake.csv');
        $truePath = $validated['true_csv']->storeAs($directory, 'True.csv');

        session([
            'training_upload_token' => $token,
            'training_upload_paths' => [
                'fake' => $fakePath,
                'true' => $truePath,
            ],
        ]);

        $message = 'Dataset files uploaded. Click Start Training when ready.';

        if ($request->expectsJson()) {
            return response()->json([
                'message' => $message,
                'token' => $token,
                'fake_rows' => CsvHelper::countDataRows(Storage::path($fakePath), 5000),
                'true_rows' => CsvHelper::countDataRows(Storage::path($truePath), 5000),
            ]);
        }

        return back()->with('status', $message);
    }

    public function start(Request $request): RedirectResponse|JsonResponse
    {
        if (TrainingJob::query()->whereIn('status', ['queued', 'running'])->exists()) {
            $message = 'A training job is already in progress.';

            return $request->expectsJson()
                ? response()->json(['message' => $message], 409)
                : back()->withErrors(['training' => $message]);
        }

        $paths = session('training_upload_paths');

        if (! is_array($paths) || empty($paths['fake']) || empty($paths['true'])) {
            $message = 'Upload Fake.csv and True.csv before starting training.';

            return $request->expectsJson()
                ? response()->json(['message' => $message], 422)
                : back()->withErrors(['training' => $message]);
        }

        $fakeStorage = Storage::path($paths['fake']);
        $trueStorage = Storage::path($paths['true']);

        if (! File::exists($fakeStorage) || ! File::exists($trueStorage)) {
            session()->forget(['training_upload_token', 'training_upload_paths']);

            return back()->withErrors(['training' => 'Uploaded files were not found. Please upload again.']);
        }

        try {
            $job = $this->training->launchJob(
                auth()->id(),
                $fakeStorage,
                $trueStorage,
                $paths['fake'],
                $paths['true'],
            );
        } catch (RuntimeException $e) {
            return $request->expectsJson()
                ? response()->json(['message' => $e->getMessage()], 503)
                : back()->withErrors(['training' => $e->getMessage()]);
        }

        session()->forget(['training_upload_token', 'training_upload_paths']);

        if ($request->expectsJson()) {
            return response()->json([
                'job_id' => $job->id,
                'ml_job_id' => $job->ml_job_id,
                'status' => 'running',
            ]);
        }

        return redirect()
            ->route('admin.training')
            ->with('status', 'Training started. Progress will update automatically.');
    }

    public function status(): JsonResponse
    {
        $job = TrainingJob::query()
            ->whereIn('status', ['queued', 'running'])
            ->latest()
            ->first();

        if (! $job) {
            $latest = TrainingJob::query()->latest()->first();

            return response()->json([
                'status' => $latest?->status ?? 'idle',
                'progress' => $latest?->progress ?? 0,
                'stage' => $latest?->stage ?? 'Idle',
                'metrics' => $latest?->metrics,
                'error' => $latest?->error_message,
                'job_id' => $latest?->id,
                'finished_at' => $latest?->finished_at?->toIso8601String(),
            ]);
        }

        try {
            $remote = $this->training->getStatus();
        } catch (RuntimeException $e) {
            return response()->json([
                'status' => $job->status,
                'progress' => $job->progress,
                'stage' => $job->stage ?? 'Waiting for ML service...',
                'error' => $e->getMessage(),
                'job_id' => $job->id,
            ]);
        }

        $remoteStatus = $remote['status'] ?? 'running';
        $wasRunning = $job->isActive();

        $nextStatus = match ($remoteStatus) {
            'completed' => 'completed',
            'failed' => 'failed',
            'idle' => in_array($job->status, ['queued', 'running'], true) ? $job->status : 'running',
            default => 'running',
        };

        $nextStage = match (true) {
            $remoteStatus === 'idle' && $job->status === 'queued' => 'Waiting for ML worker to start...',
            default => $remote['stage'] ?? $job->stage,
        };

        $job->update([
            'status' => $nextStatus,
            'progress' => (int) ($remote['progress'] ?? $job->progress),
            'stage' => $nextStage,
            'metrics' => $remote['metrics'] ?? $job->metrics,
            'error_message' => $remote['error'] ?? $job->error_message,
            'finished_at' => in_array($nextStatus, ['completed', 'failed'], true) ? now() : null,
        ]);

        if ($wasRunning && $job->status === 'completed') {
            $this->training->reloadModel();
            AuditLogger::log('training.completed', auth()->id(), TrainingJob::class, $job->id, [
                'metrics' => $job->metrics,
            ]);
        }

        if ($wasRunning && $job->status === 'failed') {
            AuditLogger::log('training.failed', auth()->id(), TrainingJob::class, $job->id, [
                'error' => $job->error_message,
            ]);
        }

        return response()->json([
            'status' => $job->status,
            'progress' => $job->progress,
            'stage' => $job->stage,
            'metrics' => $job->metrics,
            'error' => $job->error_message,
            'job_id' => $job->id,
            'started_at' => $job->started_at?->toIso8601String(),
            'finished_at' => $job->finished_at?->toIso8601String(),
        ]);
    }

    private function assertKaggleFilename(UploadedFile $file, string $expected): void
    {
        if (strcasecmp($file->getClientOriginalName(), $expected) !== 0) {
            throw ValidationException::withMessages([
                str_contains(strtolower($expected), 'fake') ? 'fake_csv' : 'true_csv' => "File must be named {$expected}.",
            ]);
        }
    }

    private function assertMinimumRows(UploadedFile $file, string $field, int $minimum): void
    {
        if (! CsvHelper::hasMinimumRows($file->getRealPath(), $minimum)) {
            throw ValidationException::withMessages([
                $field => "Each CSV must contain at least {$minimum} data rows.",
            ]);
        }
    }
}
