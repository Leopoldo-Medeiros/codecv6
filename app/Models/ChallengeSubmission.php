<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChallengeSubmission extends Model
{
    protected $fillable = [
        'user_id',
        'challenge_id',
        'code',
        'passed',
        'failed_count',
        'duration_ms',
    ];

    protected function casts(): array
    {
        return [
            'passed' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function challenge(): BelongsTo
    {
        return $this->belongsTo(Challenge::class);
    }
}
