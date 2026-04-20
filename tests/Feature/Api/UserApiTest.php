<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\DataProvider;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/**
 * Full CRUD coverage for /api/users.
 * Covers admin-only listing, self-service profile access,
 * validation of all profile fields, and GDPR endpoints.
 */
class UserApiTest extends TestCase
{
    use RefreshDatabase;

    private const PASSWORD = 'ValidPass1!';

    private User $admin;

    private User $consultant;

    private User $client;

    private User $otherClient;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);

        $this->admin = User::factory()->create(['password' => Hash::make(self::PASSWORD)]);
        $this->admin->assignRole('admin');

        $this->consultant = User::factory()->create(['password' => Hash::make(self::PASSWORD)]);
        $this->consultant->assignRole('consultant');

        $this->client = User::factory()->create(['password' => Hash::make(self::PASSWORD)]);
        $this->client->assignRole('client');

        $this->otherClient = User::factory()->create(['password' => Hash::make(self::PASSWORD)]);
        $this->otherClient->assignRole('client');
    }

    // ── Index (admin only) ────────────────────────────────────

    public function test_admin_can_list_all_users(): void
    {
        $this->actingAs($this->admin, 'sanctum')
            ->getJson('/api/users')
            ->assertOk()
            ->assertJsonStructure(['data', 'meta']);
    }

    public function test_users_list_is_paginated(): void
    {
        User::factory()->count(12)->create();

        $response = $this->actingAs($this->admin, 'sanctum')
            ->getJson('/api/users?per_page=5')
            ->assertOk();

        $this->assertCount(5, $response->json('data'));
    }

    public function test_admin_can_search_users_by_name(): void
    {
        User::factory()->create(['fullname' => 'Unique Username Person']);

        $response = $this->actingAs($this->admin, 'sanctum')
            ->getJson('/api/users?search=Unique+Username+Person')
            ->assertOk();

        $this->assertCount(1, $response->json('data'));
    }

    public function test_admin_can_search_users_by_email(): void
    {
        User::factory()->create(['email' => 'findable@uniquedomain.ie']);

        $response = $this->actingAs($this->admin, 'sanctum')
            ->getJson('/api/users?search=findable@uniquedomain.ie')
            ->assertOk();

        $names = collect($response->json('data'))->pluck('email')->toArray();
        $this->assertContains('findable@uniquedomain.ie', $names);
    }

    #[DataProvider('nonAdminRoleProvider')]
    public function test_non_admin_cannot_list_users(string $role): void
    {
        $this->actingAs($this->userForRole($role), 'sanctum')
            ->getJson('/api/users')
            ->assertForbidden();
    }

    public static function nonAdminRoleProvider(): array
    {
        return [
            'consultant' => ['consultant'],
            'client' => ['client'],
        ];
    }

    public function test_unauthenticated_cannot_list_users(): void
    {
        $this->getJson('/api/users')->assertUnauthorized();
    }

    // ── Show ──────────────────────────────────────────────────

    public function test_admin_can_view_any_user(): void
    {
        $this->actingAs($this->admin, 'sanctum')
            ->getJson("/api/users/{$this->client->id}")
            ->assertOk()
            ->assertJsonPath('data.id', $this->client->id);
    }

    public function test_client_can_view_own_profile(): void
    {
        $this->actingAs($this->client, 'sanctum')
            ->getJson("/api/users/{$this->client->id}")
            ->assertOk()
            ->assertJsonPath('data.id', $this->client->id);
    }

    public function test_client_cannot_view_another_users_profile(): void
    {
        $this->actingAs($this->client, 'sanctum')
            ->getJson("/api/users/{$this->otherClient->id}")
            ->assertForbidden();
    }

    public function test_show_returns_404_for_missing_user(): void
    {
        $this->actingAs($this->admin, 'sanctum')
            ->getJson('/api/users/99999')
            ->assertNotFound();
    }

    public function test_unauthenticated_cannot_view_user(): void
    {
        $this->getJson("/api/users/{$this->client->id}")->assertUnauthorized();
    }

    public function test_show_includes_profile_and_roles(): void
    {
        $response = $this->actingAs($this->admin, 'sanctum')
            ->getJson("/api/users/{$this->client->id}")
            ->assertOk();

        $data = $response->json('data');
        $this->assertArrayHasKey('profile', $data);
        $this->assertArrayHasKey('role', $data);
    }

    // ── Store (admin only) ────────────────────────────────────

    public function test_admin_can_create_user(): void
    {
        $clientRoleId = Role::where('name', 'client')->first()->id;

        $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/users', [
                'fullname' => 'New Client',
                'email' => 'newclient@example.com',
                'password' => self::PASSWORD,
                'password_confirmation' => self::PASSWORD,
                'role' => $clientRoleId,
                'profile' => ['profession' => 'Developer'],
            ])->assertCreated()
            ->assertJsonStructure(['message', 'user']);
    }

    #[DataProvider('nonAdminRoleProvider')]
    public function test_non_admin_cannot_create_user(string $role): void
    {
        $clientRoleId = Role::where('name', 'client')->first()->id;

        $this->actingAs($this->userForRole($role), 'sanctum')
            ->postJson('/api/users', [
                'fullname' => 'Hack User',
                'email' => 'hack@example.com',
                'password' => self::PASSWORD,
                'password_confirmation' => self::PASSWORD,
                'role' => $clientRoleId,
                'profile' => ['profession' => 'Hacker'],
            ])->assertForbidden();
    }

    public function test_unauthenticated_cannot_create_user(): void
    {
        $this->postJson('/api/users', [])->assertUnauthorized();
    }

    public function test_created_user_persisted_to_database(): void
    {
        $clientRoleId = Role::where('name', 'client')->first()->id;

        $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/users', [
                'fullname' => 'Stored User',
                'email' => 'stored@example.com',
                'password' => self::PASSWORD,
                'password_confirmation' => self::PASSWORD,
                'role' => $clientRoleId,
                'profile' => ['profession' => 'Tester'],
            ]);

        $this->assertDatabaseHas('users', ['email' => 'stored@example.com']);
    }

    #[DataProvider('invalidUserStoreProvider')]
    public function test_user_creation_fails_validation(array $overrides): void
    {
        $clientRoleId = Role::where('name', 'client')->first()->id;

        $valid = [
            'fullname' => 'Valid Name',
            'email' => 'valid@example.com',
            'password' => self::PASSWORD,
            'password_confirmation' => self::PASSWORD,
            'role' => $clientRoleId,
            'profile' => ['profession' => 'Tester'],
        ];

        $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/users', array_merge($valid, $overrides))
            ->assertUnprocessable();
    }

    public static function invalidUserStoreProvider(): array
    {
        return [
            'missing fullname' => [['fullname' => '']],
            'fullname too long' => [['fullname' => str_repeat('x', 256)]],
            'missing email' => [['email' => '']],
            'invalid email' => [['email' => 'not-an-email']],
            'missing password' => [['password' => '', 'password_confirmation' => '']],
            'password too short' => [['password' => 'Abc1', 'password_confirmation' => 'Abc1']],
            'password no uppercase' => [['password' => 'lowercase1', 'password_confirmation' => 'lowercase1']],
            'password no number' => [['password' => 'NoNumbers!', 'password_confirmation' => 'NoNumbers!']],
            'password confirmation miss' => [['password_confirmation' => 'WrongConf1!']],
            'invalid role id' => [['role' => 99999]],
            'invalid profile.website' => [['profile' => ['website' => 'not-a-url']]],
            'invalid profile.github' => [['profile' => ['github' => 'not-a-url']]],
            'invalid profile.linkedin' => [['profile' => ['linkedin' => 'not-a-url']]],
            'invalid profile.instagram' => [['profile' => ['instagram' => 'not-a-url']]],
            'invalid profile.facebook' => [['profile' => ['facebook' => 'not-a-url']]],
            'invalid birth date' => [['profile' => ['birth_date' => 'not-a-date']]],
        ];
    }

    // ── Update ────────────────────────────────────────────────

    public function test_admin_can_update_any_user(): void
    {
        $clientRoleId = Role::where('name', 'client')->first()->id;

        $this->actingAs($this->admin, 'sanctum')
            ->putJson("/api/users/{$this->client->id}", [
                'fullname' => 'Updated Name',
                'email' => $this->client->email,
                'role' => $clientRoleId,
                'profile' => ['profession' => 'Developer'],
            ])->assertOk();
    }

    public function test_client_can_update_own_profile(): void
    {
        $clientRoleId = Role::where('name', 'client')->first()->id;

        $this->actingAs($this->client, 'sanctum')
            ->putJson("/api/users/{$this->client->id}", [
                'fullname' => 'Self Updated',
                'email' => $this->client->email,
                'role' => $clientRoleId,
                'profile' => ['profession' => 'Designer'],
            ])->assertOk();
    }

    public function test_client_cannot_update_another_users_profile(): void
    {
        $clientRoleId = Role::where('name', 'client')->first()->id;

        $this->actingAs($this->client, 'sanctum')
            ->putJson("/api/users/{$this->otherClient->id}", [
                'fullname' => 'Hack',
                'email' => $this->otherClient->email,
                'role' => $clientRoleId,
                'profile' => ['profession' => 'Hacker'],
            ])->assertForbidden();
    }

    public function test_patch_update_works(): void
    {
        $clientRoleId = Role::where('name', 'client')->first()->id;

        $this->actingAs($this->admin, 'sanctum')
            ->patchJson("/api/users/{$this->client->id}", [
                'fullname' => 'Patched',
                'email' => $this->client->email,
                'role' => $clientRoleId,
                'profile' => ['profession' => 'Dev'],
            ])->assertOk();
    }

    public function test_update_allows_same_email_for_same_user(): void
    {
        $clientRoleId = Role::where('name', 'client')->first()->id;

        $this->actingAs($this->admin, 'sanctum')
            ->putJson("/api/users/{$this->client->id}", [
                'fullname' => 'Same Email User',
                'email' => $this->client->email,
                'role' => $clientRoleId,
                'profile' => ['profession' => 'Dev'],
            ])->assertOk();
    }

    public function test_update_rejects_email_already_taken_by_another_user(): void
    {
        $this->actingAs($this->admin, 'sanctum')
            ->putJson("/api/users/{$this->client->id}", [
                'fullname' => 'Test',
                'email' => $this->otherClient->email,
                'profile' => ['profession' => 'Dev'],
            ])->assertUnprocessable();
    }

    #[DataProvider('invalidUserUpdateProvider')]
    public function test_user_update_fails_validation(array $overrides): void
    {
        $valid = [
            'fullname' => 'Valid Name',
            'email' => $this->client->email,
            'profile' => [],
        ];

        $this->actingAs($this->admin, 'sanctum')
            ->putJson("/api/users/{$this->client->id}", array_merge($valid, $overrides))
            ->assertUnprocessable();
    }

    public static function invalidUserUpdateProvider(): array
    {
        return [
            'empty fullname' => [['fullname' => '']],
            'fullname too long' => [['fullname' => str_repeat('x', 256)]],
            'empty email' => [['email' => '']],
            'invalid email' => [['email' => 'not-an-email']],
            'weak password' => [['password' => 'weak', 'password_confirmation' => 'weak']],
            'password no uppercase' => [['password' => 'lowercase1', 'password_confirmation' => 'lowercase1']],
            'password no number' => [['password' => 'NoNumbers!', 'password_confirmation' => 'NoNumbers!']],
            'invalid profile.website' => [['profile' => ['website' => 'not-a-url']]],
            'invalid profile.github' => [['profile' => ['github' => 'not-a-url']]],
            'invalid profile.linkedin' => [['profile' => ['linkedin' => 'not-a-url']]],
            'invalid birth_date' => [['profile' => ['birth_date' => 'not-a-date']]],
        ];
    }

    public function test_update_password_is_optional(): void
    {
        $clientRoleId = Role::where('name', 'client')->first()->id;

        $this->actingAs($this->admin, 'sanctum')
            ->putJson("/api/users/{$this->client->id}", [
                'fullname' => 'No Pass Change',
                'email' => $this->client->email,
                'role' => $clientRoleId,
                'profile' => ['profession' => 'Developer'],
            ])->assertOk();
    }

    // ── Destroy (admin only) ──────────────────────────────────

    public function test_admin_can_delete_user(): void
    {
        $user = User::factory()->create();
        $user->assignRole('client');

        $this->actingAs($this->admin, 'sanctum')
            ->deleteJson("/api/users/{$user->id}")
            ->assertOk()
            ->assertJsonPath('message', 'User deleted successfully');
    }

    #[DataProvider('nonAdminRoleProvider')]
    public function test_non_admin_cannot_delete_user(string $role): void
    {
        $this->actingAs($this->userForRole($role), 'sanctum')
            ->deleteJson("/api/users/{$this->otherClient->id}")
            ->assertForbidden();
    }

    public function test_admin_cannot_delete_themselves(): void
    {
        $this->actingAs($this->admin, 'sanctum')
            ->deleteJson("/api/users/{$this->admin->id}")
            ->assertForbidden();
    }

    public function test_delete_removes_user_from_accessible_records(): void
    {
        $user = User::factory()->create();
        $user->assignRole('client');
        $userId = $user->id;

        $this->actingAs($this->admin, 'sanctum')
            ->deleteJson("/api/users/{$userId}")
            ->assertOk();

        // User is deleted — admin listing should no longer contain this user
        $response = $this->actingAs($this->admin, 'sanctum')
            ->getJson('/api/users?per_page=100')
            ->assertOk();

        $ids = collect($response->json('data'))->pluck('id')->toArray();
        $this->assertNotContains($userId, $ids);
    }

    // ── GDPR ──────────────────────────────────────────────────

    public function test_client_can_export_own_data(): void
    {
        $this->actingAs($this->client, 'sanctum')
            ->getJson("/api/users/{$this->client->id}/export")
            ->assertOk()
            ->assertJsonStructure(['exported_at', 'account', 'profile']);
    }

    public function test_export_includes_correct_account_data(): void
    {
        $response = $this->actingAs($this->client, 'sanctum')
            ->getJson("/api/users/{$this->client->id}/export")
            ->assertOk();

        $this->assertSame($this->client->id, $response->json('account.id'));
        $this->assertSame($this->client->email, $response->json('account.email'));
    }

    public function test_admin_can_export_any_users_data(): void
    {
        $this->actingAs($this->admin, 'sanctum')
            ->getJson("/api/users/{$this->client->id}/export")
            ->assertOk();
    }

    public function test_client_cannot_export_another_users_data(): void
    {
        $this->actingAs($this->client, 'sanctum')
            ->getJson("/api/users/{$this->otherClient->id}/export")
            ->assertForbidden();
    }

    public function test_unauthenticated_cannot_export_data(): void
    {
        $this->getJson("/api/users/{$this->client->id}/export")->assertUnauthorized();
    }

    public function test_admin_can_erase_client_data(): void
    {
        $this->actingAs($this->admin, 'sanctum')
            ->deleteJson("/api/users/{$this->client->id}/erase")
            ->assertOk()
            ->assertJsonPath('message', 'User data permanently erased.');
    }

    public function test_erase_removes_user_from_database(): void
    {
        $victim = User::factory()->create();
        $victim->assignRole('client');

        $this->actingAs($this->admin, 'sanctum')
            ->deleteJson("/api/users/{$victim->id}/erase");

        $this->assertDatabaseMissing('users', ['id' => $victim->id]);
    }

    public function test_client_cannot_erase_user_data(): void
    {
        $this->actingAs($this->client, 'sanctum')
            ->deleteJson("/api/users/{$this->otherClient->id}/erase")
            ->assertForbidden();
    }

    public function test_unauthenticated_cannot_erase_user(): void
    {
        $this->deleteJson("/api/users/{$this->client->id}/erase")->assertUnauthorized();
    }

    // ── Roles listing (admin only) ────────────────────────────

    public function test_admin_can_list_roles(): void
    {
        $this->actingAs($this->admin, 'sanctum')
            ->getJson('/api/roles')
            ->assertOk();
    }

    #[DataProvider('nonAdminRoleProvider')]
    public function test_non_admin_cannot_list_roles(string $role): void
    {
        $this->actingAs($this->userForRole($role), 'sanctum')
            ->getJson('/api/roles')
            ->assertForbidden();
    }

    public function test_unauthenticated_cannot_list_roles(): void
    {
        $this->getJson('/api/roles')->assertUnauthorized();
    }

    // ── Helpers ───────────────────────────────────────────────

    private function userForRole(string $role): User
    {
        return match ($role) {
            'admin' => $this->admin,
            'consultant' => $this->consultant,
            'client' => $this->client,
        };
    }
}
