<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * The public, unauthenticated view of a teaser challenge (practice funnel
 * stage 1 / F2). Deliberately narrower than ChallengeResource: no
 * tests_code (an anonymous, unaccountable visitor could scrape the answer
 * key), no is_premium/price_eur/created_by (irrelevant to a free surface).
 */
class TeaserChallengeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
            'difficulty' => $this->difficulty->value,
            'boilerplate_code' => $this->boilerplate_code,
        ];
    }
}
