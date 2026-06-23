<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Dataset;
use App\Models\Prediction;
use App\Models\User;
use App\Services\AuditLogger;
use App\Support\CsvHelper;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function overview(): View
    {
        $stats = [
            'total_predictions' => Prediction::count(),
            'real_count' => Prediction::where('result', 'REAL')->count(),
            'fake_count' => Prediction::where('result', 'FAKE')->count(),
            'uncertain_count' => Prediction::where('result', 'UNCERTAIN')->count(),
            'total_users' => User::where('role', 'user')->count(),
        ];

        $recent = Prediction::with('user')->latest()->limit(10)->get();

        return view('admin.overview', compact('stats', 'recent'));
    }

    public function users(Request $request): View
    {
        $query = User::query()->where('role', 'user');

        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%");
            });
        }

        $users = $query->withCount('predictions')->latest()->paginate(15);

        return view('admin.users', compact('users', 'search'));
    }

    public function toggleUser(User $user): RedirectResponse
    {
        abort_if($user->isAdmin(), 403);

        $user->update(['is_active' => ! $user->is_active]);

        AuditLogger::log(
            $user->is_active ? 'user.activated' : 'user.deactivated',
            auth()->id(),
            User::class,
            $user->id,
            ['email' => $user->email],
        );

        return back()->with('status', 'User status updated.');
    }

    public function predictions(Request $request): View
    {
        $query = Prediction::with('user')->latest();

        if ($result = $request->query('result')) {
            $query->where('result', $result);
        }

        if ($from = $request->query('from')) {
            $query->whereDate('created_at', '>=', $from);
        }

        if ($to = $request->query('to')) {
            $query->whereDate('created_at', '<=', $to);
        }

        $predictions = $query->paginate(20);

        return view('admin.predictions', compact('predictions'));
    }

    public function auditLogs(): View
    {
        $logs = AuditLog::with('user')->latest()->paginate(25);

        return view('admin.audit', compact('logs'));
    }

    public function datasets(): View
    {
        $datasets = Dataset::with('uploader')->latest()->paginate(15);

        return view('admin.datasets', compact('datasets'));
    }

    public function storeDataset(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'dataset' => ['required', 'file', 'mimes:csv,txt', 'max:102400'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $file = $validated['dataset'];
        $path = $file->store('datasets');
        $absolutePath = Storage::path($path);
        $rowCount = CsvHelper::countDataRows($absolutePath);

        $dataset = Dataset::create([
            'uploaded_by' => auth()->id(),
            'filename' => $path,
            'original_name' => $file->getClientOriginalName(),
            'row_count' => $rowCount,
            'status' => 'processed',
            'notes' => $validated['notes'] ?? null,
        ]);

        AuditLogger::log('dataset.uploaded', auth()->id(), Dataset::class, $dataset->id, [
            'filename' => $dataset->original_name,
            'rows' => $rowCount,
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Dataset uploaded successfully.',
                'dataset' => [
                    'id' => $dataset->id,
                    'name' => $dataset->original_name,
                    'row_count' => $rowCount,
                    'status' => $dataset->status,
                ],
            ]);
        }

        return back()->with('status', 'Dataset uploaded successfully.');
    }
}
