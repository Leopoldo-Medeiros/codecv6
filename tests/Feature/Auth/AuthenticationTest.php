<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Notifications\WelcomeNotification;
use Database\Seeders\RoleSeeder;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * Covers login, register, logout, refresh, and /me endpoints.
 * Data providers drive many invalid-input combinations so a single
 * test method covers the full rule surface without repetition.
 */
class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    private const STRONG_PASSWORD = 'ValidPass1!';

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
    }

    // ── Login ─────────────────────────────────────────────────

    public function test_user_can_login_with_valid_credentials(): void
    {
        $user = User::factory()->create(['password' => Hash::make(self::STRONG_PASSWORD)]);

        $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => self::STRONG_PASSWORD,
        ])->assertOk()->assertJsonStructure(['user', 'access_token', 'token_type']);
    }

    public function test_login_returns_user_with_correct_email(): void
    {
        $user = User::factory()->create(['password' => Hash::make(self::STRONG_PASSWORD)]);

        $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => self::STRONG_PASSWORD,
        ])->assertOk()->assertJsonPath('user.email', $user->email);
    }

    #[DataProvider('invalidLoginProvider')]
    public function test_login_fails_with_invalid_credentials(string $email, string $password): void
    {
        User::factory()->create([
            'email' => 'real@example.com',
            'password' => Hash::make(self::STRONG_PASSWORD),
        ]);

        $this->postJson('/api/login', compact('email', 'password'))
            ->assertUnauthorized();
    }

    public static function invalidLoginProvider(): array
    {
        return [
            'wrong password' => ['real@example.com', 'WrongPassword1!'],
            'wrong email' => ['ghost@example.com', 'ValidPass1!'],
            'both wrong' => ['no@no.com', 'BadPass0!'],
            'password lowercase only' => ['real@example.com', 'allowercase'],
        ];
    }

    #[DataProvider('missingLoginFieldProvider')]
    public function test_login_requires_both_fields(array $payload): void
    {
        $this->postJson('/api/login', $payload)->assertUnprocessable();
    }

    public static function missingLoginFieldProvider(): array
    {
        return [
            'no email' => [['password' => 'ValidPass1!']],
            'no password' => [['email' => 'test@test.com']],
            'empty body' => [[]],
        ];
    }

    public function test_login_rejects_malformed_email(): void
    {
        $this->postJson('/api/login', ['email' => 'not-an-email', 'password' => 'ValidPass1!'])
            ->assertUnprocessable();
    }

    // ── Register ──────────────────────────────────────────────

    public function test_welcome_email_sent_on_registration(): void
    {
        Notification::fake();

        $this->postJson('/api/register', [
            'fullname' => 'Welcome User',
            'email' => 'welcome@example.com',
            'password' => self::STRONG_PASSWORD,
            'password_confirmation' => self::STRONG_PASSWORD,
        ])->assertCreated();

        $user = User::where('email', 'welcome@example.com')->first();
        Notification::assertSentTo($user, WelcomeNotification::class);
    }

    public function test_user_can_register_with_valid_data(): void
    {
        $this->postJson('/api/register', [
            'fullname' => 'John Doe',
            'email' => 'john@example.com',
            'password' => self::STRONG_PASSWORD,
            'password_confirmation' => self::STRONG_PASSWORD,
        ])->assertCreated()->assertJsonStructure(['user', 'access_token']);
    }

    public function test_registration_assigns_client_role_by_default(): void
    {
        $this->postJson('/api/register', [
            'fullname' => 'Jane Doe',
            'email' => 'jane@example.com',
            'password' => self::STRONG_PASSWORD,
            'password_confirmation' => self::STRONG_PASSWORD,
        ])->assertCreated();

        $this->assertDatabaseHas('model_has_roles', [
            'model_id' => User::where('email', 'jane@example.com')->first()->id,
        ]);
    }

    public function test_registration_persists_user_to_database(): void
    {
        $this->postJson('/api/register', [
            'fullname' => 'Stored User',
            'email' => 'stored@example.com',
            'password' => self::STRONG_PASSWORD,
            'password_confirmation' => self::STRONG_PASSWORD,
        ]);

        $this->assertDatabaseHas('users', ['email' => 'stored@example.com']);
    }

    #[DataProvider('weakPasswordProvider')]
    public function test_registration_rejects_weak_password(string $password): void
    {
        $this->postJson('/api/register', [
            'fullname' => 'Test User',
            'email' => 'test@example.com',
            'password' => $password,
            'password_confirmation' => $password,
        ])->assertUnprocessable();
    }

    public static function weakPasswordProvider(): array
    {
        return [
            'too short' => ['Aa1!'],
            'no uppercase' => ['lowercase1!'],
            'no lowercase' => ['UPPERCASE1!'],
            'no number' => ['NoNumberHere!'],
            'all digits' => ['12345678'],
            'just spaces' => ['        '],
            'seven chars with all' => ['Abc123!'],
        ];
    }

    #[DataProvider('invalidRegistrationProvider')]
    public function test_registration_validates_required_fields(array $overrides): void
    {
        $valid = [
            'fullname' => 'Valid Name',
            'email' => 'valid@example.com',
            'password' => self::STRONG_PASSWORD,
            'password_confirmation' => self::STRONG_PASSWORD,
        ];

        $this->postJson('/api/register', array_merge($valid, $overrides))
            ->assertUnprocessable();
    }

    public static function invalidRegistrationProvider(): array
    {
        return [
            'missing fullname' => [['fullname' => '']],
            'fullname too long' => [['fullname' => str_repeat('a', 256)]],
            'missing email' => [['email' => '']],
            'invalid email format' => [['email' => 'not-an-email']],
            'email missing domain' => [['email' => 'user@']],
            'missing password' => [['password' => '', 'password_confirmation' => '']],
            'confirmation mismatch' => [['password_confirmation' => 'DifferentPass1!']],
            'invalid birth date' => [['profile' => ['birth_date' => 'not-a-date']]],
            'profession too long' => [['profile' => ['profession' => str_repeat('x', 256)]]],
        ];
    }

    public function test_registration_rejects_duplicate_email(): void
    {
        User::factory()->create(['email' => 'taken@example.com']);

        $this->postJson('/api/register', [
            'fullname' => 'Another User',
            'email' => 'taken@example.com',
            'password' => self::STRONG_PASSWORD,
            'password_confirmation' => self::STRONG_PASSWORD,
        ])->assertUnprocessable();
    }

    // ── Logout & Session ──────────────────────────────────────

    public function test_authenticated_user_can_logout(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $this->withToken($token)
            ->postJson('/api/logout')
            ->assertOk()
            ->assertJsonPath('message', 'Successfully logged out');
    }

    public function test_unauthenticated_user_cannot_logout(): void
    {
        $this->postJson('/api/logout')->assertUnauthorized();
    }

    public function test_authenticated_user_can_get_own_profile(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user, 'sanctum')
            ->getJson('/api/me')
            ->assertOk()
            ->assertJsonStructure(['user']);
    }

    public function test_me_returns_correct_user(): void
    {
        $user = User::factory()->create(['email' => 'me@example.com']);

        $this->actingAs($user, 'sanctum')
            ->getJson('/api/me')
            ->assertOk()
            ->assertJsonPath('user.email', 'me@example.com');
    }

    public function test_unauthenticated_user_cannot_access_me(): void
    {
        $this->getJson('/api/me')->assertUnauthorized();
    }

    public function test_refresh_returns_new_token(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $this->withToken($token)
            ->postJson('/api/refresh')
            ->assertOk()
            ->assertJsonStructure(['access_token']);
    }

    public function test_unauthenticated_user_cannot_refresh(): void
    {
        $this->postJson('/api/refresh')->assertUnauthorized();
    }

    // ── Profile image in auth responses ───────────────────────

    public function test_login_response_includes_profile_image_url(): void
    {
        $user = User::factory()->create(['password' => Hash::make(self::STRONG_PASSWORD)]);

        $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => self::STRONG_PASSWORD,
        ])->assertOk()
            ->assertJsonPath('user.profile.profile_image_url', null);
    }

    public function test_register_response_includes_profile_image_url(): void
    {
        $this->postJson('/api/register', [
            'fullname' => 'Image User',
            'email' => 'imguser@example.com',
            'password' => self::STRONG_PASSWORD,
            'password_confirmation' => self::STRONG_PASSWORD,
        ])->assertCreated()
            ->assertJsonPath('user.profile.profile_image_url', null);
    }

    public function test_refresh_response_includes_profile_image_url(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $this->withToken($token)
            ->postJson('/api/refresh')
            ->assertOk()
            ->assertJsonPath('user.profile.profile_image_url', null);
    }

    // ── Email Verification ────────────────────────────────────

    public function test_verify_email_notification_sent_on_registration(): void
    {
        Notification::fake();

        $this->postJson('/api/register', [
            'fullname' => 'Verify User',
            'email' => 'verify@example.com',
            'password' => self::STRONG_PASSWORD,
            'password_confirmation' => self::STRONG_PASSWORD,
        ])->assertCreated();

        $user = User::where('email', 'verify@example.com')->first();
        Notification::assertSentTo($user, VerifyEmail::class);
        Notification::assertSentTo($user, WelcomeNotification::class);
    }

    public function test_email_verification_marks_user_verified(): void
    {
        $user = User::factory()->unverified()->create();

        $url = URL::temporarySignedRoute(
            'api.email.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)],
        );

        $this->get($url)->assertRedirect();

        $this->assertNotNull($user->fresh()->email_verified_at);
    }

    public function test_expired_verification_link_is_rejected(): void
    {
        $user = User::factory()->unverified()->create();

        $url = URL::temporarySignedRoute(
            'api.email.verify',
            now()->subMinutes(1),
            ['id' => $user->id, 'hash' => sha1($user->email)],
        );

        $this->get($url)->assertForbidden();

        $this->assertNull($user->fresh()->email_verified_at);
    }

    public function test_verification_link_redirects_to_frontend_dashboard(): void
    {
        config(['app.frontend_url' => 'http://localhost:3000']);
        $user = User::factory()->unverified()->create();

        $url = URL::temporarySignedRoute(
            'api.email.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)],
        );

        $this->get($url)->assertRedirectContains('localhost:3000/dashboard?verified=1');
    }

    public function test_already_verified_user_is_redirected_without_error(): void
    {
        $user = User::factory()->create(['email_verified_at' => now()]);

        $url = URL::temporarySignedRoute(
            'api.email.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)],
        );

        $this->get($url)->assertRedirect();
    }
}
