<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/**
 * Coverage for /api/linkedin/analyze — previously untested despite calling
 * Gemini with a user-uploaded PDF (docs/architecture-review.md Phase 1).
 * Every Gemini call is faked; Http::preventStrayRequests() (tests/TestCase)
 * means a missed fake fails the test instead of hitting the real API.
 */
class LinkedInAnalysisTest extends TestCase
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
        return UploadedFile::fake()->create('linkedin.pdf', $kilobytes, 'application/pdf');
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

    public function test_analyze_requires_a_linkedin_pdf(): void
    {
        $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/linkedin/analyze', ['target_role' => 'Backend Engineer'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('linkedin_pdf');
    }

    public function test_analyze_rejects_non_pdf_file(): void
    {
        $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/linkedin/analyze', [
                'linkedin_pdf' => UploadedFile::fake()->create('profile.docx', 100, 'application/msword'),
                'target_role' => 'Backend Engineer',
            ])->assertUnprocessable()
            ->assertJsonValidationErrors('linkedin_pdf');
    }

    public function test_analyze_rejects_file_over_5mb(): void
    {
        $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/linkedin/analyze', [
                'linkedin_pdf' => $this->fakePdf(5121),
                'target_role' => 'Backend Engineer',
            ])->assertUnprocessable()
            ->assertJsonValidationErrors('linkedin_pdf');
    }

    public function test_analyze_requires_target_role(): void
    {
        $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/linkedin/analyze', ['linkedin_pdf' => $this->fakePdf()])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('target_role');
    }

    public function test_analyze_rejects_target_role_under_5_chars(): void
    {
        $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/linkedin/analyze', [
                'linkedin_pdf' => $this->fakePdf(),
                'target_role' => 'Dev',
            ])->assertUnprocessable()
            ->assertJsonValidationErrors('target_role');
    }

    public function test_analyze_rejects_years_exp_over_50(): void
    {
        $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/linkedin/analyze', [
                'linkedin_pdf' => $this->fakePdf(),
                'target_role' => 'Backend Engineer',
                'years_exp' => 51,
            ])->assertUnprocessable()
            ->assertJsonValidationErrors('years_exp');
    }

    public function test_unauthenticated_cannot_analyze(): void
    {
        $this->postJson('/api/linkedin/analyze', ['target_role' => 'Backend Engineer'])
            ->assertUnauthorized();
    }

    // ── Configuration ─────────────────────────────────────────

    public function test_returns_503_when_gemini_is_not_configured(): void
    {
        config(['services.gemini.key' => null]);

        $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/linkedin/analyze', [
                'linkedin_pdf' => $this->fakePdf(),
                'target_role' => 'Backend Engineer',
            ])->assertStatus(503);
    }

    // ── Success ───────────────────────────────────────────────

    public function test_analyze_returns_parsed_gemini_result(): void
    {
        $this->fakeGemini($this->geminiTextResponse(json_encode([
            'score' => 75,
            'headline_suggestion' => 'Senior Backend Engineer | PHP & Laravel',
            'summary_suggestion' => 'Experienced backend engineer...',
            'strengths' => ['Strong PHP background'],
            'gaps' => ['No cloud certifications'],
            'skills_to_add' => ['AWS'],
            'recommendations' => ['Add a certification'],
            'overall' => 'Solid candidate.',
        ])));

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/linkedin/analyze', [
                'linkedin_pdf' => $this->fakePdf(),
                'target_role' => 'Senior Backend Engineer',
                'industry' => 'Fintech',
                'years_exp' => 6,
            ])->assertOk();

        $response->assertJson(['score' => 75, 'overall' => 'Solid candidate.']);
    }

    public function test_analyze_parses_json_wrapped_in_markdown_fences(): void
    {
        $json = json_encode(['score' => 55, 'overall' => 'Needs work.']);
        $this->fakeGemini($this->geminiTextResponse("```json\n{$json}\n```"));

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/linkedin/analyze', [
                'linkedin_pdf' => $this->fakePdf(),
                'target_role' => 'Backend Engineer',
            ])->assertOk();

        $response->assertJson(['score' => 55]);
    }

    // ── Upstream failure ──────────────────────────────────────

    public function test_analyze_returns_502_when_gemini_request_fails(): void
    {
        $this->fakeGemini(['error' => 'boom'], 500);

        $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/linkedin/analyze', [
                'linkedin_pdf' => $this->fakePdf(),
                'target_role' => 'Backend Engineer',
            ])->assertStatus(502);
    }

    public function test_analyze_returns_500_when_gemini_response_is_unparseable(): void
    {
        $this->fakeGemini($this->geminiTextResponse('Sorry, I cannot help with that.'));

        $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/linkedin/analyze', [
                'linkedin_pdf' => $this->fakePdf(),
                'target_role' => 'Backend Engineer',
            ])->assertStatus(500);
    }
}
