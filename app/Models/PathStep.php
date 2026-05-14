<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PathStep extends Model
{
    use HasFactory;

    protected $fillable = [
        'path_id',
        'course_id',
        'title',
        'description',
        'tldr',
        'concept_content',
        'estimated_minutes',
        'difficulty',
        'prerequisites',
        'concepts',
        'has_playground',
        'playground_starter_code',
        'resources',
        'order',
        'type',
        'lab_url',
        'instructions',
        'challenge_prompt',
        'challenge_slug',
    ];

    protected $casts = [
        'resources' => 'array',
        'instructions' => 'array',
        'prerequisites' => 'array',
        'concepts' => 'array',
        'has_playground' => 'boolean',
        'estimated_minutes' => 'integer',
        'order' => 'integer',
    ];

    public function path(): BelongsTo
    {
        return $this->belongsTo(Path::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function challenge(): BelongsTo
    {
        return $this->belongsTo(Challenge::class, 'challenge_slug', 'slug');
    }

    public function userProgress(): HasMany
    {
        return $this->hasMany(UserStepProgress::class, 'path_step_id');
    }
}
