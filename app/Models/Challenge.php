<?php

namespace App\Models;

use App\Enums\ChallengeDifficulty;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Challenge extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'difficulty',
        'boilerplate_code',
        'tests_code',
        'is_premium',
        'price_eur',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'difficulty' => ChallengeDifficulty::class,
            'is_premium' => 'boolean',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
