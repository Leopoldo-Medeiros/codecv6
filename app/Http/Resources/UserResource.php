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
            'profile' => $this->when($this->relationLoaded('profile'), function () {
                return $this->profile ? [
                    'birth_date' => $this->profile->birth_date,
                    'profession' => $this->profile->profession,
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
