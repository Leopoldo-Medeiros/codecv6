<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class JobResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'company' => $this->company,
            'location' => $this->location,
            'salary' => $this->salary,
            'consultant' => $this->when($this->relationLoaded('consultant'), function () {
                return $this->consultant ? [
                    'id' => $this->consultant->id,
                    'fullname' => $this->consultant->fullname,
                ] : null;
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
