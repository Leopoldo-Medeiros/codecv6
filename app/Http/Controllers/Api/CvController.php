<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class CvController extends Controller
{
    public function analyze(Request $request): JsonResponse
    {
        $request->validate([
            'cv' => 'required|file|mimes:pdf|max:5120',
            'job_description' => 'required_without:job_url|nullable|string|min:50|max:15000',
            'job_url' => 'required_without:job_description|nullable|url|max:2048',
        ]);

        $apiKey = config('services.gemini.key');
        if (! $apiKey) {
            return response()->json(['message' => 'CV analysis is not configured yet.'], 503);
        }

        $jobDescription = $request->input('job_description');

        if ($request->filled('job_url')) {
            $jobDescription = $this->fetchJobFromUrl($request->input('job_url'));

            if (! $jobDescription) {
                return response()->json(['message' => 'Could not read the job posting from that URL. Try pasting the text instead.'], 422);
            }
        }

        $pdfBase64 = base64_encode(
            file_get_contents($request->file('cv')->getRealPath())
        );

        $prompt = <<<PROMPT
You are a professional CV / resume expert specialising in the Irish and European tech job market.

Analyse the attached CV against the job description below and return ONLY a valid JSON object — no markdown, no explanation outside the JSON.

JSON structure required:
{
  "score": <integer 0-100, overall match percentage>,
  "matched_keywords": [<strings — skills/technologies/terms that appear in BOTH the CV and the job description>],
  "missing_keywords": [<strings — important skills/terms in the job description that are absent from the CV>],
  "strengths": [<strings — 3-5 specific strengths of this candidate for this role>],
  "improvements": [<strings — 3-5 concrete, actionable suggestions to improve the CV for this role>],
  "summary": "<2-3 sentence overall assessment>"
}

Job description:
{$jobDescription}
PROMPT;

        $model = config('services.gemini.model', 'gemini-flash-latest');
        $endpoint = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";

        $response = Http::timeout(60)
            ->post($endpoint, [
                'contents' => [
                    [
                        'parts' => [
                            [
                                'inline_data' => [
                                    'mime_type' => 'application/pdf',
                                    'data' => $pdfBase64,
                                ],
                            ],
                            [
                                'text' => $prompt,
                            ],
                        ],
                    ],
                ],
                'generationConfig' => [
                    'maxOutputTokens' => 8192,
                ],
            ]);

        if ($response->failed()) {
            return response()->json([
                'message' => 'Analysis failed. Please try again later.',
            ], 502);
        }

        $raw = $response->json('candidates.0.content.parts.0.text', '{}');
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

    private function fetchJobFromUrl(string $url): ?string
    {
        try {
            $response = Http::timeout(15)
                ->withHeaders(['Accept' => 'text/plain'])
                ->get('https://r.jina.ai/'.$url);
        } catch (ConnectionException) {
            return null;
        }

        if ($response->failed()) {
            return null;
        }

        $text = trim($response->body());

        if (strlen($text) < 50) {
            return null;
        }

        return mb_substr($text, 0, 15000);
    }
}
