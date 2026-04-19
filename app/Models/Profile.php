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
    ];

    protected $casts = [
        'stack' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
