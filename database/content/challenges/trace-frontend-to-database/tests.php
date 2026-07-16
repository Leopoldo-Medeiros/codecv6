<?php

use PHPUnit\Framework\TestCase;

class TraceBugTest extends TestCase
{
    public function test_validation_catches_missing_name(): void
    {
        $request = new Request(['email' => 'test@example.com']);
        $result = handleProfileUpdate($request, 1);

        $this->assertArrayHasKey('error', $result);
        $this->assertSame('Validation failed', $result['error']);
    }

    public function test_validation_catches_missing_email(): void
    {
        $request = new Request(['name' => 'John']);
        $result = handleProfileUpdate($request, 1);

        $this->assertArrayHasKey('error', $result);
    }

    public function test_valid_request_returns_success(): void
    {
        $request = new Request([
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);
        $result = handleProfileUpdate($request, 1);

        $this->assertTrue($result['success']);
        $this->assertSame('John Doe', $result['profile']['name']);
    }

    public function test_update_preserves_existing_fields(): void
    {
        $service = new ProfileService();

        $service->updateProfile(1, [
            'name' => 'John',
            'email' => 'john@example.com',
            'bio' => 'Developer',
        ]);

        $service->updateProfile(1, [
            'name' => 'John Updated',
            'email' => 'john@example.com',
        ]);

        $profile = $service->getProfile(1);
        $this->assertSame('John Updated', $profile['name']);
        $this->assertSame('john@example.com', $profile['email']);
    }
}
