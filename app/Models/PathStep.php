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
        'resources',
        'order',
    ];

    protected $casts = [
        'resources' => 'array',
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

    public function userProgress(): HasMany
    {
        return $this->hasMany(UserStepProgress::class, 'path_step_id');
    }
}
