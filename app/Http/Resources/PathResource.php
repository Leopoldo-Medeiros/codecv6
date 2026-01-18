<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PathResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'consultant' => $this->when($this->relationLoaded('consultant'), function () {
                return $this->consultant ? [
                    'id' => $this->consultant->id,
                    'fullname' => $this->consultant->fullname,
                ] : null;
            }),
            'plans' => $this->when($this->relationLoaded('plans'), function () {
                return $this->plans->map(fn ($plan) => [
                    'id' => $plan->id,
                    'name' => $plan->name,
                ]);
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
