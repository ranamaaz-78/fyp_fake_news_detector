<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Prediction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'input_text',
        'result',
        'confidence',
        'confidence_level',
        'model_used',
    ];

    protected function casts(): array
    {
        return [
            'confidence' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function preview(int $length = 120): string
    {
        return strlen($this->input_text) > $length
            ? substr($this->input_text, 0, $length).'…'
            : $this->input_text;
    }
}
