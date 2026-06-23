<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrainingJob extends Model
{
    protected $fillable = [
        'started_by',
        'ml_job_id',
        'status',
        'progress',
        'stage',
        'metrics',
        'error_message',
        'fake_csv_path',
        'true_csv_path',
        'started_at',
        'finished_at',
    ];

    protected function casts(): array
    {
        return [
            'metrics' => 'array',
            'started_at' => 'datetime',
            'finished_at' => 'datetime',
        ];
    }

    public function starter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'started_by');
    }

    public function isActive(): bool
    {
        return in_array($this->status, ['queued', 'running'], true);
    }
}
