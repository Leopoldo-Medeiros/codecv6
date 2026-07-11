<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Regression coverage for Phase 0 step 0.5 (docs/architecture-review.md):
 * the legacy Blade GET /profile/{id} route had no ownership check at all —
 * any authenticated user could view any other user's full profile.
 */
class LegacyProfileTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
    }

    public function test_user_can_view_own_legacy_profile(): void
    {
        $user = User::factory()->create();
        $user->assignRole('client');

        $this->actingAs($user)
            ->get("/profile/{$user->id}")
            ->assertOk();
    }

    public function test_user_cannot_view_another_users_legacy_profile(): void
    {
        $user = User::factory()->create();
        $user->assignRole('client');
        $other = User::factory()->create();
        $other->assignRole('client');

        $this->actingAs($user)
            ->get("/profile/{$other->id}")
            ->assertForbidden();
    }

    public function test_admin_can_view_any_legacy_profile(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $other = User::factory()->create();
        $other->assignRole('client');

        $this->actingAs($admin)
            ->get("/profile/{$other->id}")
            ->assertOk();
    }

    public function test_guest_cannot_view_legacy_profile(): void
    {
        $user = User::factory()->create();

        $this->get("/profile/{$user->id}")
            ->assertRedirect('/login');
    }
}
