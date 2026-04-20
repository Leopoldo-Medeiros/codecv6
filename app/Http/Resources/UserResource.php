<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'fullname' => $this->fullname,
            'email' => $this->email,
            'email_verified_at' => $this->email_verified_at,
            'role' => $this->roles->first()?->name,
            'consultant_id' => $this->consultant_id,
            'consultant' => $this->when($this->relationLoaded('consultant'), function () {
                return $this->consultant ? [
                    'id' => $this->consultant->id,
                    'fullname' => $this->consultant->fullname,
                    'email' => $this->consultant->email,
                ] : null;
            }),
            'profile' => $this->when($this->relationLoaded('profile'), function () {
                return $this->profile ? [
                    'birth_date' => $this->profile->birth_date,
                    'profession' => $this->profile->profession,
                    'goal' => $this->profile->goal,
                    'level' => $this->profile->level,
                    'stack' => $this->profile->stack,
                    'product_interest' => $this->profile->product_interest,
                    'availability_hours' => $this->profile->availability_hours,
                    'timeline' => $this->profile->timeline,
                    'profile_image' => $this->profile->profile_image,
                    'profile_image_url' => $this->profile->profile_image
                        ? asset('storage/'.$this->profile->profile_image)
                        : null,
                    'website' => $this->profile->website,
                    'github' => $this->profile->github,
                    'linkedin' => $this->profile->linkedin,
                    'instagram' => $this->profile->instagram,
                    'facebook' => $this->profile->facebook,
                ] : null;
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
