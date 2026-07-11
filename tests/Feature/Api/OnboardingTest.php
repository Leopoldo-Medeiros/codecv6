<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Notifications\NewClientOnboarded;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * Coverage for PATCH /api/me/onboarding — previously untested
 * (docs/architecture-review.md Phase 1).
 */
class OnboardingTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);

        $this->user = User::factory()->create(['needs_onboarding' => true]);
        $this->user->assignRole('client');
    }

    private function validPayload(array $overrides = []): array
    {
        return array_merge([
            'profession' => 'Backend Developer',
            'level' => 'mid',
            'stack' => ['PHP', 'Laravel'],
            'product_interest' => 'bootcamp',
            'availability_hours' => 10,
            'timeline' => '3-6m',
            'goal' => 'Land a senior role',
        ], $overrides);
    }

    public function test_completes_onboarding_with_valid_data(): void
    {
        Notification::fake();

        $response = $this->actingAs($this->user, 'sanctum')
            ->patchJson('/api/me/onboarding', $this->validPayload())
            ->assertOk()
            ->assertJson(['message' => 'Onboarding complete']);

        $this->assertFalse((bool) $response->json('user.needs_onboarding'));

        $this->user->refresh();
        $this->assertFalse((bool) $this->user->needs_onboarding);
        $this->assertSame('Backend Developer', $this->user->profile->profession);
        $this->assertSame(['PHP', 'Laravel'], $this->user->profile->stack);
    }

    public function test_notifies_every_admin_on_completion(): void
    {
        Notification::fake();
        $admin1 = User::factory()->create();
        $admin1->assignRole('admin');
        $admin2 = User::factory()->create();
        $admin2->assignRole('admin');

        $this->actingAs($this->user, 'sanctum')
            ->patchJson('/api/me/onboarding', $this->validPayload())
            ->assertOk();

        Notification::assertSentTo([$admin1, $admin2], NewClientOnboarded::class);
    }

    public function test_unauthenticated_cannot_complete_onboarding(): void
    {
        $this->patchJson('/api/me/onboarding', $this->validPayload())
            ->assertUnauthorized();
    }

    #[DataProvider('invalidPayloadProvider')]
    public function test_validates_required_and_enum_fields(array $overrides): void
    {
        Notification::fake();

        $this->actingAs($this->user, 'sanctum')
            ->patchJson('/api/me/onboarding', $this->validPayload($overrides))
            ->assertUnprocessable();
    }

    public static function invalidPayloadProvider(): array
    {
        return [
            'missing profession' => [['profession' => '']],
            'invalid level' => [['level' => 'ceo']],
            'empty stack' => [['stack' => []]],
            'too many stack items' => [['stack' => array_fill(0, 21, 'X')]],
            'invalid product_interest' => [['product_interest' => 'enterprise']],
            'availability_hours too low' => [['availability_hours' => 0]],
            'availability_hours too high' => [['availability_hours' => 81]],
            'invalid timeline' => [['timeline' => 'someday']],
            'missing goal' => [['goal' => '']],
        ];
    }
}
