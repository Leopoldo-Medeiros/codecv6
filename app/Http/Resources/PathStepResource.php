<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PathStepResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'path_id' => $this->path_id,
            'title' => $this->title,
            'description' => $this->description,
            'tldr' => $this->tldr,
            'concept_content' => $this->concept_content,
            'estimated_minutes' => $this->estimated_minutes,
            'difficulty' => $this->difficulty,
            'prerequisites' => $this->prerequisites ?? [],
            'concepts' => $this->concepts ?? [],
            'has_playground' => (bool) $this->has_playground,
            'playground_starter_code' => $this->playground_starter_code,
            'type' => $this->type ?? 'reading',
            'lab_url' => $this->lab_url,
            'instructions' => $this->instructions ?? [],
            'challenge_prompt' => $this->challenge_prompt,
            'challenge_slug' => $this->challenge_slug,
            // Quiz questions with the answer key stripped — id/question/
            // options only. Grading is server-side (PathStepController::
            // submitQuiz), so correct_index/explanation never reach the client.
            'quiz' => $this->when(
                is_array($this->quiz) && $this->quiz !== [],
                fn () => array_map(fn (array $q) => [
                    'id' => $q['id'],
                    'question' => $q['question'],
                    'options' => $q['options'],
                ], $this->quiz)
            ),
            'challenge' => $this->when(
                $this->relationLoaded('challenge') && $this->challenge,
                fn () => new ChallengeResource($this->challenge)
            ),
            'resources' => $this->resources ?? [],
            'order' => $this->order,
            'course' => $this->when($this->relationLoaded('course') && $this->course, fn () => [
                'id' => $this->course->id,
                'name' => $this->course->name,
                'slug' => $this->course->slug,
            ]),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
