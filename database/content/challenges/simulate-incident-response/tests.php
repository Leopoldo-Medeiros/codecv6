<?php

use PHPUnit\Framework\TestCase;

class IncidentResponseTest extends TestCase
{
    public function test_detects_null_emails(): void
    {
        $users = [
            ['id' => 1, 'email' => 'alice@example.com', 'role' => 'admin'],
            ['id' => 2, 'email' => null, 'role' => 'user'],
            ['id' => 3, 'email' => 'charlie@example.com', 'role' => 'user'],
        ];

        $ir = new IncidentResponse();
        $ir->detectAnomalies($users);
        $plan = $ir->generateRecoveryPlan();

        $this->assertNotEmpty($plan);
    }

    public function test_detects_invalid_roles(): void
    {
        $users = [
            ['id' => 1, 'email' => 'alice@example.com', 'role' => 'admin'],
            ['id' => 2, 'email' => 'bob@example.com', 'role' => 'superadmin'],
        ];

        $ir = new IncidentResponse();
        $ir->detectAnomalies($users);

        $result = $ir->verifyIntegrity($users);
        $this->assertGreaterThan(0, $result['invalid']);
    }

    public function test_execute_recovery_fixes_issues(): void
    {
        $users = [
            ['id' => 1, 'email' => null, 'role' => 'user'],
            ['id' => 2, 'email' => 'bob@example.com', 'role' => 'admin'],
        ];

        $ir = new IncidentResponse();
        $ir->detectAnomalies($users);
        $result = $ir->executeRecovery($users);

        $this->assertGreaterThan(0, $result['fixed']);
    }

    public function test_verify_integrity_returns_valid_count(): void
    {
        $users = [
            ['id' => 1, 'email' => 'valid@example.com', 'role' => 'admin'],
            ['id' => 2, 'email' => 'valid2@example.com', 'role' => 'user'],
        ];

        $ir = new IncidentResponse();
        $result = $ir->verifyIntegrity($users);

        $this->assertSame(2, $result['valid']);
        $this->assertSame(0, $result['invalid']);
    }

    public function test_clean_data_has_no_anomalies(): void
    {
        $users = [
            ['id' => 1, 'email' => 'a@example.com', 'role' => 'admin'],
            ['id' => 2, 'email' => 'b@example.com', 'role' => 'user'],
        ];

        $ir = new IncidentResponse();
        $ir->detectAnomalies($users);
        $plan = $ir->generateRecoveryPlan();

        $this->assertEmpty($plan);
    }
}
