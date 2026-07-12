<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserProgressStats extends Model
{
    protected $fillable = [
        'user_id',
        'xp_points',
        'current_streak',
        'longest_streak',
        'last_practiced_at',
    ];

    protected function casts(): array
    {
        return [
            'xp_points' => 'integer',
            'current_streak' => 'integer',
            'longest_streak' => 'integer',
            'last_practiced_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
