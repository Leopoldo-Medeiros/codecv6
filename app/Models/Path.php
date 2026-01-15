<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Path extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'consultant_id',
    ];

    /**
     * Get the consultant that owns the path.
     */
    public function consultant()
    {
        return $this->belongsTo(User::class, 'consultant_id');
    }

    /**
     * Get the plans that this path is associated with.
     */
    public function plans()
    {
        return $this->belongsToMany(Plan::class, 'path_plan', 'path_id', 'plan_id')
            ->withTimestamps();
    }
}
