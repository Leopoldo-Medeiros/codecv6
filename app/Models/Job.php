<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Job extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'company',
        'location',
        'salary',
        'consultant_id',
    ];

    /**
     * Get the consultant that owns the job.
     */
    public function consultant()
    {
        return $this->belongsTo(User::class, 'consultant_id');
    }
}
