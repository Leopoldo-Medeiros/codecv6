<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\WaitlistEntry;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Coverage for the "coming soon" waitlist — the demand-sensing instrument:
 * anonymous join, idempotency, topic allow-list, and the admin readout.
 */
class WaitlistApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
    }

    public function test_anonymous_visitor_can_join_a_waitlist(): void
    {
        $response = $this->postJson('/api/public/waitlist', [
            'email' => 'lead@example.com',
            'topic' => 'ai-for-support',
            'source' => 'homepage',
        ]);

        $response->assertCreated()->assertJsonStructure(['message']);
        $this->assertDatabaseHas('waitlist_entries', [
            'email' => 'lead@example.com',
            'topic' => 'ai-for-support',
            'source' => 'homepage',
            'user_id' => null,
        ]);
    }

    public function test_signing_up_twice_for_the_same_topic_does_not_duplicate(): void
    {
        $payload = ['email' => 'dup@example.com', 'topic' => 'observability'];

        $this->postJson('/api/public/waitlist', $payload)->assertCreated();
        $this->postJson('/api/public/waitlist', $payload)->assertCreated();

        $this->assertSame(1, WaitlistEntry::where('email', 'dup@example.com')->where('topic', 'observability')->count());
    }

    public function test_same_email_may_join_different_topics(): void
    {
        $this->postJson('/api/public/waitlist', ['email' => 'keen@example.com', 'topic' => 'observability'])->assertCreated();
        $this->postJson('/api/public/waitlist', ['email' => 'keen@example.com', 'topic' => 'ai-for-devs'])->assertCreated();

        $this->assertSame(2, WaitlistEntry::where('email', 'keen@example.com')->count());
    }

    public function test_unknown_topic_is_rejected(): void
    {
        $this->postJson('/api/public/waitlist', [
            'email' => 'lead@example.com',
            'topic' => 'crypto-trading',
        ])->assertStatus(422)->assertJsonValidationErrors('topic');
    }

    public function test_invalid_email_is_rejected(): void
    {
        $this->postJson('/api/public/waitlist', [
            'email' => 'not-an-email',
            'topic' => 'observability',
        ])->assertStatus(422)->assertJsonValidationErrors('email');
    }

    public function test_logged_in_user_is_linked_to_their_signup(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user, 'sanctum')
            ->postJson('/api/public/waitlist', ['email' => $user->email, 'topic' => 'ai-for-devs'])
            ->assertCreated();

        $this->assertDatabaseHas('waitlist_entries', [
            'email' => $user->email,
            'topic' => 'ai-for-devs',
            'user_id' => $user->id,
        ]);
    }

    public function test_admin_sees_signup_counts_for_every_topic(): void
    {
        WaitlistEntry::create(['email' => 'a@example.com', 'topic' => 'ai-for-support']);
        WaitlistEntry::create(['email' => 'b@example.com', 'topic' => 'ai-for-support']);
        WaitlistEntry::create(['email' => 'c@example.com', 'topic' => 'observability']);

        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin, 'sanctum')->getJson('/api/admin/waitlist');

        $response->assertOk()
            ->assertJsonPath('total', 3)
            ->assertJsonCount(3, 'topics'); // every configured topic present, even at zero

        $support = collect($response->json('topics'))->firstWhere('topic', 'ai-for-support');
        $this->assertSame(2, $support['signups']);
    }

    public function test_non_admin_cannot_read_the_waitlist_readout(): void
    {
        $user = User::factory()->create();
        $user->assignRole('client');

        $this->actingAs($user, 'sanctum')
            ->getJson('/api/admin/waitlist')
            ->assertForbidden();
    }

    public function test_authenticated_vote_links_the_user_and_uses_account_email(): void
    {
        $user = User::factory()->create(['email' => 'member@example.com']);

        // No email in the body — a logged-in vote uses the account's email.
        $this->actingAs($user, 'sanctum')
            ->postJson('/api/waitlist', ['topic' => 'observability', 'source' => 'dashboard'])
            ->assertCreated();

        $this->assertDatabaseHas('waitlist_entries', [
            'email' => 'member@example.com',
            'topic' => 'observability',
            'user_id' => $user->id,
            'source' => 'dashboard',
        ]);
    }

    public function test_authenticated_vote_ignores_a_client_supplied_email(): void
    {
        $user = User::factory()->create(['email' => 'real@example.com']);

        $this->actingAs($user, 'sanctum')
            ->postJson('/api/waitlist', ['topic' => 'ai-for-devs', 'email' => 'spoofed@example.com'])
            ->assertCreated();

        $this->assertDatabaseHas('waitlist_entries', ['email' => 'real@example.com', 'user_id' => $user->id]);
        $this->assertDatabaseMissing('waitlist_entries', ['email' => 'spoofed@example.com']);
    }
}
