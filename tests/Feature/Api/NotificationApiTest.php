<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Notifications\ClientAssigned;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Coverage for /api/notifications — previously untested
 * (docs/architecture-review.md Phase 1).
 */
class NotificationApiTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);

        $this->user = User::factory()->create();
        $this->user->assignRole('consultant');
    }

    // ── Index ─────────────────────────────────────────────────

    public function test_index_returns_notifications_and_unread_count(): void
    {
        $client = User::factory()->create();
        $this->user->notify(new ClientAssigned($client));

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/notifications')
            ->assertOk();

        $response->assertJsonCount(1, 'notifications');
        $response->assertJsonPath('unread_count', 1);
        $response->assertJsonPath('notifications.0.type', 'client_assigned');
        $response->assertJsonPath('notifications.0.read', false);
    }

    public function test_index_only_returns_the_authenticated_users_notifications(): void
    {
        $other = User::factory()->create();
        $client = User::factory()->create();
        $other->notify(new ClientAssigned($client));

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/notifications')
            ->assertOk();

        $response->assertJsonCount(0, 'notifications');
        $response->assertJsonPath('unread_count', 0);
    }

    public function test_index_caps_results_at_30(): void
    {
        $client = User::factory()->create();
        for ($i = 0; $i < 35; $i++) {
            $this->user->notify(new ClientAssigned($client));
        }

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/notifications')
            ->assertOk();

        $response->assertJsonCount(30, 'notifications');
        $response->assertJsonPath('unread_count', 35);
    }

    public function test_unauthenticated_cannot_list_notifications(): void
    {
        $this->getJson('/api/notifications')->assertUnauthorized();
    }

    // ── Mark one as read ──────────────────────────────────────

    public function test_mark_read_marks_the_notification_as_read(): void
    {
        $client = User::factory()->create();
        $this->user->notify(new ClientAssigned($client));
        $notificationId = $this->user->notifications()->first()->id;

        $this->actingAs($this->user, 'sanctum')
            ->patchJson("/api/notifications/{$notificationId}/read")
            ->assertOk()
            ->assertJson(['message' => 'Marked as read.']);

        $this->assertNotNull($this->user->notifications()->find($notificationId)->read_at);
    }

    public function test_mark_read_returns_404_for_another_users_notification(): void
    {
        $other = User::factory()->create();
        $client = User::factory()->create();
        $other->notify(new ClientAssigned($client));
        $notificationId = $other->notifications()->first()->id;

        $this->actingAs($this->user, 'sanctum')
            ->patchJson("/api/notifications/{$notificationId}/read")
            ->assertNotFound();
    }

    public function test_mark_read_returns_404_for_unknown_id(): void
    {
        $this->actingAs($this->user, 'sanctum')
            ->patchJson('/api/notifications/'.Str::uuid().'/read')
            ->assertNotFound();
    }

    public function test_unauthenticated_cannot_mark_read(): void
    {
        $client = User::factory()->create();
        $this->user->notify(new ClientAssigned($client));
        $notificationId = $this->user->notifications()->first()->id;

        $this->patchJson("/api/notifications/{$notificationId}/read")->assertUnauthorized();
    }

    // ── Mark all as read ──────────────────────────────────────

    public function test_mark_all_read_marks_every_unread_notification(): void
    {
        $client = User::factory()->create();
        $this->user->notify(new ClientAssigned($client));
        $this->user->notify(new ClientAssigned($client));

        $this->actingAs($this->user, 'sanctum')
            ->patchJson('/api/notifications/read-all')
            ->assertOk()
            ->assertJson(['message' => 'All notifications marked as read.']);

        $this->assertSame(0, $this->user->unreadNotifications()->count());
    }

    public function test_mark_all_read_does_not_affect_other_users(): void
    {
        $other = User::factory()->create();
        $client = User::factory()->create();
        $other->notify(new ClientAssigned($client));

        $this->actingAs($this->user, 'sanctum')
            ->patchJson('/api/notifications/read-all')
            ->assertOk();

        $this->assertSame(1, $other->unreadNotifications()->count());
    }

    public function test_unauthenticated_cannot_mark_all_read(): void
    {
        $this->patchJson('/api/notifications/read-all')->assertUnauthorized();
    }
}
