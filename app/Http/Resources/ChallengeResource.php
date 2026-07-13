<?php

namespace App\Http\Resources;

use App\Services\EntitlementService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChallengeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        // `locked` tells the UI to show a lock + upsell instead of opening
        // the editor. When locked, boilerplate/tests are withheld so a free
        // user can't read gated content straight off the list endpoint.
        $user = $request->user();
        $locked = $user !== null
            && ! app(EntitlementService::class)->canAccessChallenge($user, $this->resource);

        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
            'difficulty' => $this->difficulty->value,
            'boilerplate_code' => $locked ? null : $this->boilerplate_code,
            'tests_code' => $locked ? null : $this->tests_code,
            'is_premium' => $this->is_premium,
            'price_eur' => $this->price_eur,
            'locked' => $locked,
            'created_by' => $this->created_by,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
