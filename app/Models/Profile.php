<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'birth_date',
        'profession',
        'goal',
        'level',
        'stack',
        'product_interest',
        'availability_hours',
        'timeline',
        'profile_image',
        'website',
        'github',
        'linkedin',
        'instagram',
        'facebook',
        'is_public',
        'public_slug',
    ];

    protected $casts = [
        'stack' => 'array',
        'is_public' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
