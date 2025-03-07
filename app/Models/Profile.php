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
        'profile_image',
        'website',
        'github',
        'linkedin',
        'instagram',
        'facebook',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
