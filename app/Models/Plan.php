<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Plan extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'price',
        'consultant_id',
    ];

    /**
     * Get the consultant that owns the plan.
     */
    public function consultant()
    {
        return $this->belongsTo(User::class, 'consultant_id');
    }

    /**
     * Get the users (clients) that are subscribed to this plan.
     */
    public function clients()
    {
        return $this->belongsToMany(User::class, 'plan_user', 'plan_id', 'user_id')
            ->withTimestamps();
    }

    /**
     * Get the paths that this plan is associated with.
     */
    public function paths()
    {
        return $this->belongsToMany(Path::class, 'path_plan', 'plan_id', 'path_id')
            ->withTimestamps();
    }
}
