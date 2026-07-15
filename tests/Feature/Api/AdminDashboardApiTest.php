<?php

namespace Tests\Feature\Api;

use App\Enums\PaymentStatus;
use App\Enums\PaymentTier;
use App\Models\Payment;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Coverage for GET /admin/dashboard — platform-wide aggregates (activity,
 * signups, revenue) that feed the admin dashboard's heatmap and KPI sparklines.
 * Admin-only.
 */
class AdminDashboardApiTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');
    }

    public function test_admin_gets_platform_aggregates(): void
    {
        $this->actingAs($this->admin, 'sanctum')
            ->getJson('/api/admin/dashboard')
            ->assertOk()
            ->assertJsonStructure(['activity', 'signups', 'revenue' => ['total', 'currency', 'series']]);
    }

    public function test_revenue_counts_only_paid_eur_payments(): void
    {
        $client = User::factory()->create();
        // Counted: PAID + EUR
        Payment::create(['user_id' => $client->id, 'stripe_session_id' => 'cs_a', 'tier' => PaymentTier::PRACTICE, 'amount' => 5000, 'currency' => 'eur', 'status' => PaymentStatus::PAID, 'paid_at' => now()]);
        // Excluded: BRL, and PENDING
        Payment::create(['user_id' => $client->id, 'stripe_session_id' => 'cs_b', 'tier' => PaymentTier::PRACTICE, 'amount' => 9900, 'currency' => 'brl', 'status' => PaymentStatus::PAID, 'paid_at' => now()]);
        Payment::create(['user_id' => $client->id, 'stripe_session_id' => 'cs_c', 'tier' => PaymentTier::PRACTICE, 'amount' => 3000, 'currency' => 'eur', 'status' => PaymentStatus::PENDING]);

        $this->actingAs($this->admin, 'sanctum')
            ->getJson('/api/admin/dashboard')
            ->assertOk()
            ->assertJsonPath('revenue.total', 5000);
    }

    public function test_client_cannot_access_admin_dashboard(): void
    {
        $client = User::factory()->create();
        $client->assignRole('client');

        $this->actingAs($client, 'sanctum')
            ->getJson('/api/admin/dashboard')
            ->assertForbidden();
    }

    public function test_unauthenticated_cannot_access(): void
    {
        $this->getJson('/api/admin/dashboard')->assertUnauthorized();
    }
}
