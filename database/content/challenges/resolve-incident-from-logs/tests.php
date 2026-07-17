<?php

use PHPUnit\Framework\TestCase;

class LogAnalysisTest extends TestCase
{
    public function test_identifies_database_connection_errors(): void
    {
        $logs = [
            '[2026-05-06 12:00:01] [ERROR] [/api/users] Database connection timeout',
            '[2026-05-06 12:00:02] [ERROR] [/api/orders] Database connection timeout',
            '[2026-05-06 12:00:03] [ERROR] [/api/users] Database connection timeout',
            '[2026-05-06 12:00:04] [INFO] [/api/health] Health check passed',
        ];

        $result = analyzeLogs($logs);

        $this->assertSame('database_connection', $result['root_cause']);
        $this->assertSame(3, $result['error_count']);
    }

    public function test_returns_correct_severity_for_critical(): void
    {
        $logs = array_fill(0, 10, '[2026-05-06 12:00:01] [ERROR] [/api/users] Database connection timeout');

        $result = analyzeLogs($logs);

        $this->assertSame('critical', $result['severity']);
    }

    public function test_identifies_affected_endpoints(): void
    {
        $logs = [
            '[2026-05-06 12:00:01] [ERROR] [/api/users] Server error',
            '[2026-05-06 12:00:02] [ERROR] [/api/orders] Server error',
            '[2026-05-06 12:00:03] [INFO] [/api/health] OK',
        ];

        $result = analyzeLogs($logs);

        $this->assertContains('/api/users', $result['affected_endpoints']);
        $this->assertContains('/api/orders', $result['affected_endpoints']);
        $this->assertNotContains('/api/health', $result['affected_endpoints']);
    }

    public function test_empty_logs_returns_empty_report(): void
    {
        $result = analyzeLogs([]);

        $this->assertSame('low', $result['severity']);
        $this->assertSame(0, $result['error_count']);
    }

    public function test_mixed_levels_classifies_correctly(): void
    {
        $logs = [
            '[2026-05-06 12:00:01] [WARNING] [/api/users] Slow query detected',
            '[2026-05-06 12:00:02] [INFO] [/api/health] OK',
        ];

        $result = analyzeLogs($logs);

        $this->assertSame('low', $result['severity']);
    }
}
