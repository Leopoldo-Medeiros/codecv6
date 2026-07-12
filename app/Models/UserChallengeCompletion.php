<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserChallengeCompletion extends Model
{
    // Immutable first-pass record — completed_at is the only meaningful
    // timestamp, the table has no created_at/updated_at columns.
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'challenge_id',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'completed_at' => 'datetime',
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
