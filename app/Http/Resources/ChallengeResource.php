<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChallengeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
            'difficulty' => $this->difficulty->value,
            'boilerplate_code' => $this->boilerplate_code,
            'tests_code' => $this->tests_code,
            'is_premium' => $this->is_premium,
            'price_eur' => $this->price_eur,
            'created_by' => $this->created_by,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
