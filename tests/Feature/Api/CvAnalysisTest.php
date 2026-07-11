<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/**
 * Coverage for /api/cv/analyze — previously untested despite calling Gemini
 * (and, for job-url input, Jina) with user-uploaded PDFs
 * (docs/architecture-review.md Phase 1). Every outbound call is faked;
 * Http::preventStrayRequests() (tests/TestCase) means a missed fake fails
 * the test instead of hitting the real API.
 */
class CvAnalysisTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);

        $this->user = User::factory()->create();
        $this->user->assignRole('client');
        config(['services.gemini.key' => 'test-gemini-key']);
    }

    private function fakePdf(int $kilobytes = 100): UploadedFile
    {
        return UploadedFile::fake()->create('cv.pdf', $kilobytes, 'application/pdf');
    }

    private function fakeGemini(array $body, int $status = 200): void
    {
        Http::fake(['generativelanguage.googleapis.com/*' => Http::response($body, $status)]);
    }

    private function geminiTextResponse(string $text): array
    {
        return ['candidates' => [['content' => ['parts' => [['text' => $text]]]]]];
    }

    // ── Validation ────────────────────────────────────────────

    public function test_analyze_requires_a_cv_file(): void
    {
        $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/cv/analyze', ['job_description' => str_repeat('a', 60)])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('cv');
    }

    public function test_analyze_rejects_non_pdf_file(): void
    {
        $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/cv/analyze', [
                'cv' => UploadedFile::fake()->create('cv.docx', 100, 'application/msword'),
                'job_description' => str_repeat('a', 60),
            ])->assertUnprocessable()
            ->assertJsonValidationErrors('cv');
    }

    public function test_analyze_rejects_file_over_5mb(): void
    {
        $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/cv/analyze', [
                'cv' => $this->fakePdf(5121),
                'job_description' => str_repeat('a', 60),
            ])->assertUnprocessable()
            ->assertJsonValidationErrors('cv');
    }

    public function test_analyze_requires_job_description_or_job_url(): void
    {
        $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/cv/analyze', ['cv' => $this->fakePdf()])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['job_description', 'job_url']);
    }

    public function test_analyze_rejects_job_description_under_50_chars(): void
    {
        $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/cv/analyze', [
                'cv' => $this->fakePdf(),
                'job_description' => 'too short',
            ])->assertUnprocessable()
            ->assertJsonValidationErrors('job_description');
    }

    public function test_analyze_rejects_invalid_job_url(): void
    {
        $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/cv/analyze', [
                'cv' => $this->fakePdf(),
                'job_url' => 'not-a-url',
            ])->assertUnprocessable()
            ->assertJsonValidationErrors('job_url');
    }

    public function test_unauthenticated_cannot_analyze(): void
    {
        $this->postJson('/api/cv/analyze', ['job_description' => str_repeat('a', 60)])
            ->assertUnauthorized();
    }

    // ── Configuration ─────────────────────────────────────────

    public function test_returns_503_when_gemini_is_not_configured(): void
    {
        config(['services.gemini.key' => null]);

        $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/cv/analyze', [
                'cv' => $this->fakePdf(),
                'job_description' => str_repeat('a', 60),
            ])->assertStatus(503);
    }

    // ── Success ───────────────────────────────────────────────

    public function test_analyze_returns_parsed_gemini_result(): void
    {
        $this->fakeGemini($this->geminiTextResponse(json_encode([
            'score' => 82,
            'matched_keywords' => ['PHP', 'Laravel'],
            'missing_keywords' => ['Kubernetes'],
            'strengths' => ['Strong backend skills'],
            'improvements' => ['Add cloud experience'],
            'summary' => 'Good match overall.',
        ])));

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/cv/analyze', [
                'cv' => $this->fakePdf(),
                'job_description' => str_repeat('Backend developer role. ', 5),
            ])->assertOk();

        $response->assertJson(['score' => 82, 'summary' => 'Good match overall.']);
    }

    public function test_analyze_parses_json_wrapped_in_markdown_fences(): void
    {
        $json = json_encode(['score' => 70, 'summary' => 'Decent fit.']);
        $this->fakeGemini($this->geminiTextResponse("Here is the analysis:\n```json\n{$json}\n```"));

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/cv/analyze', [
                'cv' => $this->fakePdf(),
                'job_description' => str_repeat('a', 60),
            ])->assertOk();

        $response->assertJson(['score' => 70]);
    }

    public function test_analyze_via_job_url_fetches_via_jina_then_calls_gemini(): void
    {
        Http::fake([
            'r.jina.ai/*' => Http::response(str_repeat('Senior PHP developer wanted. ', 5), 200),
            'generativelanguage.googleapis.com/*' => Http::response(
                $this->geminiTextResponse(json_encode(['score' => 60, 'summary' => 'ok'])),
            ),
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/cv/analyze', [
                'cv' => $this->fakePdf(),
                'job_url' => 'https://example.com/jobs/123',
            ])->assertOk();

        $response->assertJson(['score' => 60]);
        Http::assertSent(fn ($request) => str_contains($request->url(), 'r.jina.ai/https://example.com/jobs/123'));
    }

    public function test_analyze_via_job_url_returns_422_when_jina_returns_too_little_text(): void
    {
        Http::fake(['r.jina.ai/*' => Http::response('short', 200)]);

        $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/cv/analyze', [
                'cv' => $this->fakePdf(),
                'job_url' => 'https://example.com/jobs/123',
            ])->assertStatus(422);
    }

    public function test_analyze_via_job_url_returns_422_when_jina_is_down(): void
    {
        Http::fake(['r.jina.ai/*' => Http::response('', 500)]);

        $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/cv/analyze', [
                'cv' => $this->fakePdf(),
                'job_url' => 'https://example.com/jobs/123',
            ])->assertStatus(422);
    }

    // ── Upstream failure ──────────────────────────────────────

    public function test_analyze_returns_502_when_gemini_request_fails(): void
    {
        $this->fakeGemini(['error' => 'boom'], 500);

        $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/cv/analyze', [
                'cv' => $this->fakePdf(),
                'job_description' => str_repeat('a', 60),
            ])->assertStatus(502);
    }

    public function test_analyze_returns_500_when_gemini_response_is_unparseable(): void
    {
        $this->fakeGemini($this->geminiTextResponse('Sorry, I cannot help with that.'));

        $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/cv/analyze', [
                'cv' => $this->fakePdf(),
                'job_description' => str_repeat('a', 60),
            ])->assertStatus(500);
    }
}
