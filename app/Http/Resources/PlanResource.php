<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PlanResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'consultant' => $this->when($this->relationLoaded('consultant'), function () {
                return $this->consultant ? [
                    'id' => $this->consultant->id,
                    'fullname' => $this->consultant->fullname,
                ] : null;
            }),
            'clients' => $this->when($this->relationLoaded('clients'), function () {
                return $this->clients->map(fn ($client) => [
                    'id' => $client->id,
                    'fullname' => $client->fullname,
                ]);
            }),
            'paths' => $this->when($this->relationLoaded('paths'), function () {
                return $this->paths->map(fn ($path) => [
                    'id' => $path->id,
                    'name' => $path->name,
                ]);
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
