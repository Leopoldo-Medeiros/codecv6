<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class PasswordResetApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
        Notification::fake();
    }

    // ── Forgot Password ───────────────────────────────────────

    public function test_forgot_password_returns_success_for_existing_email(): void
    {
        $user = User::factory()->create();

        $this->postJson('/api/forgot-password', ['email' => $user->email])
            ->assertOk()
            ->assertJsonFragment(['message' => 'If that email exists, a reset link has been sent.']);
    }

    public function test_forgot_password_returns_success_for_unknown_email(): void
    {
        $this->postJson('/api/forgot-password', ['email' => 'nobody@example.com'])
            ->assertOk()
            ->assertJsonFragment(['message' => 'If that email exists, a reset link has been sent.']);
    }

    public function test_forgot_password_requires_email(): void
    {
        $this->postJson('/api/forgot-password', [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['email']);
    }

    public function test_forgot_password_rejects_invalid_email_format(): void
    {
        $this->postJson('/api/forgot-password', ['email' => 'not-an-email'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['email']);
    }

    public function test_forgot_password_sends_notification_to_existing_user(): void
    {
        $user = User::factory()->create();

        $this->postJson('/api/forgot-password', ['email' => $user->email]);

        Notification::assertSentTo($user, ResetPasswordNotification::class);
    }

    public function test_forgot_password_does_not_send_notification_for_unknown_email(): void
    {
        $this->postJson('/api/forgot-password', ['email' => 'nobody@example.com']);

        Notification::assertNothingSent();
    }

    // ── Reset Password ────────────────────────────────────────

    public function test_password_can_be_reset_with_valid_token(): void
    {
        $user  = User::factory()->create();
        $token = Password::broker()->createToken($user);

        $this->postJson('/api/reset-password', [
            'token'                 => $token,
            'email'                 => $user->email,
            'password'              => 'new-password-123',
            'password_confirmation' => 'new-password-123',
        ])->assertOk()
          ->assertJsonFragment(['message' => 'Password reset successfully. You can now sign in.']);
    }

    public function test_password_is_actually_updated_in_database(): void
    {
        $user  = User::factory()->create();
        $token = Password::broker()->createToken($user);

        $this->postJson('/api/reset-password', [
            'token'                 => $token,
            'email'                 => $user->email,
            'password'              => 'new-password-123',
            'password_confirmation' => 'new-password-123',
        ]);

        $this->assertTrue(Hash::check('new-password-123', $user->fresh()->password));
    }

    public function test_reset_password_fails_with_invalid_token(): void
    {
        $user = User::factory()->create();

        $this->postJson('/api/reset-password', [
            'token'                 => 'invalid-token',
            'email'                 => $user->email,
            'password'              => 'new-password-123',
            'password_confirmation' => 'new-password-123',
        ])->assertUnprocessable()
          ->assertJsonFragment(['message' => 'This reset link is invalid or has expired.']);
    }

    public function test_reset_password_fails_with_unknown_email(): void
    {
        $this->postJson('/api/reset-password', [
            'token'                 => 'some-token',
            'email'                 => 'nobody@example.com',
            'password'              => 'new-password-123',
            'password_confirmation' => 'new-password-123',
        ])->assertUnprocessable();
    }

    public function test_reset_password_fails_when_passwords_do_not_match(): void
    {
        $user  = User::factory()->create();
        $token = Password::broker()->createToken($user);

        $this->postJson('/api/reset-password', [
            'token'                 => $token,
            'email'                 => $user->email,
            'password'              => 'new-password-123',
            'password_confirmation' => 'different-password',
        ])->assertUnprocessable()
          ->assertJsonValidationErrors(['password']);
    }

    public function test_reset_password_fails_when_password_too_short(): void
    {
        $user  = User::factory()->create();
        $token = Password::broker()->createToken($user);

        $this->postJson('/api/reset-password', [
            'token'                 => $token,
            'email'                 => $user->email,
            'password'              => 'short',
            'password_confirmation' => 'short',
        ])->assertUnprocessable()
          ->assertJsonValidationErrors(['password']);
    }

    public function test_reset_password_requires_all_fields(): void
    {
        $this->postJson('/api/reset-password', [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['token', 'email', 'password', 'password_confirmation']);
    }
}
