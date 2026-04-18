<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class LinkedInController extends Controller
{
    public function analyze(Request $request): JsonResponse
    {
        $request->validate([
            'linkedin_pdf' => 'required|file|mimes:pdf|max:5120',
            'target_role'  => 'required|string|min:5|max:200',
            'industry'     => 'nullable|string|max:100',
            'years_exp'    => 'nullable|integer|min:0|max:50',
        ]);

        $apiKey = config('services.gemini.key');
        if (! $apiKey) {
            return response()->json(['message' => 'LinkedIn analysis is not configured yet.'], 503);
        }

        $targetRole = $request->input('target_role');
        $industry   = $request->input('industry') ? "Industry/location: {$request->input('industry')}" : '';
        $yearsExp   = $request->input('years_exp') !== null ? "Years of experience: {$request->input('years_exp')}" : '';

        $prompt = <<<PROMPT
You are a professional career coach and LinkedIn profile expert specialising in the Irish and European tech job market.

Analyse the LinkedIn profile below against the candidate's career goal and return ONLY a valid JSON object — no markdown, no explanation outside the JSON.

Career goal: {$targetRole}
{$industry}
{$yearsExp}

JSON structure required:
{
  "score": <integer 0-100, how aligned the current profile is with the career goal>,
  "headline_suggestion": "<improved LinkedIn headline tailored to the target role>",
  "summary_suggestion": "<improved About section — 3-4 sentences, first person, keyword-rich>",
  "strengths": [<strings — 3-5 profile strengths relevant to the target role>],
  "gaps": [<strings — 3-5 gaps or weaknesses that could hinder reaching the goal>],
  "skills_to_add": [<strings — skills/certifications/tools missing from profile but valued for target role>],
  "recommendations": [<strings — 3-5 specific, actionable steps to improve the profile>],
  "overall": "<2-3 sentence overall assessment of the candidate's readiness for the target role>"
}
PROMPT;

        $model    = config('services.gemini.model', 'gemini-flash-latest');
        $endpoint = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";

        $pdfBase64 = base64_encode(
            file_get_contents($request->file('linkedin_pdf')->getRealPath())
        );

        $response = Http::timeout(60)->post($endpoint, [
            'contents' => [[
                'parts' => [
                    ['inline_data' => ['mime_type' => 'application/pdf', 'data' => $pdfBase64]],
                    ['text' => $prompt],
                ],
            ]],
            'generationConfig' => ['maxOutputTokens' => 8192],
        ]);

        if ($response->failed()) {
            return response()->json(['message' => 'Analysis failed. Please try again later.'], 502);
        }

        $raw  = $response->json('candidates.0.content.parts.0.text', '{}');
        $data = json_decode($raw, true);

        if (json_last_error() !== JSON_ERROR_NONE || ! isset($data['score'])) {
            if (preg_match('/\{.*\}/s', $raw, $m)) {
                $data = json_decode($m[0], true);
            }
        }

        if (! is_array($data) || ! isset($data['score'])) {
            return response()->json(['message' => 'Could not parse analysis response.'], 500);
        }

        return response()->json($data);
    }

}
