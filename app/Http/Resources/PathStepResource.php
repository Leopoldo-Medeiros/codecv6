<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PathStepResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'               => $this->id,
            'path_id'          => $this->path_id,
            'title'            => $this->title,
            'description'      => $this->description,
            'type'             => $this->type ?? 'reading',
            'lab_url'          => $this->lab_url,
            'instructions'     => $this->instructions ?? [],
            'challenge_prompt' => $this->challenge_prompt,
            'resources'        => $this->resources ?? [],
            'order'            => $this->order,
            'course'           => $this->when($this->relationLoaded('course') && $this->course, fn () => [
                'id'   => $this->course->id,
                'name' => $this->course->name,
                'slug' => $this->course->slug,
            ]),
            'created_at'       => $this->created_at,
            'updated_at'       => $this->updated_at,
        ];
    }
}
