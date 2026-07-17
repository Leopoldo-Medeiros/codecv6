<?php

use PHPUnit\Framework\TestCase;

class MetricsCollectorTest extends TestCase
{
    public function test_records_successful_requests(): void
    {
        $collector = new MetricsCollector();

        $collector->startRequest();
        usleep(1000);
        $collector->endRequest(200);

        $snapshot = $collector->snapshot();

        $this->assertSame(1, $snapshot['total_requests']);
        $this->assertSame(0, $snapshot['total_errors']);
    }

    public function test_records_error_requests(): void
    {
        $collector = new MetricsCollector();

        $collector->startRequest();
        $collector->endRequest(500);

        $snapshot = $collector->snapshot();

        $this->assertSame(1, $snapshot['total_errors']);
        $this->assertSame(100.0, $snapshot['error_rate']);
    }

    public function test_calculates_correct_error_rate(): void
    {
        $collector = new MetricsCollector();

        $collector->startRequest(); $collector->endRequest(200);
        $collector->startRequest(); $collector->endRequest(500);
        $collector->startRequest(); $collector->endRequest(200);
        $collector->startRequest(); $collector->endRequest(404);

        $snapshot = $collector->snapshot();

        $this->assertSame(4, $snapshot['total_requests']);
        $this->assertSame(2, $snapshot['total_errors']);
        $this->assertSame(50.0, $snapshot['error_rate']);
    }

    public function test_snapshot_returns_avg_duration(): void
    {
        $collector = new MetricsCollector();

        $collector->startRequest();
        $collector->endRequest(200);

        $snapshot = $collector->snapshot();

        $this->assertIsFloat($snapshot['avg_duration_ms']);
        $this->assertGreaterThan(0, $snapshot['avg_duration_ms']);
    }

    public function test_empty_snapshot_has_zeroes(): void
    {
        $collector = new MetricsCollector();
        $snapshot = $collector->snapshot();

        $this->assertSame(0, $snapshot['total_requests']);
        $this->assertSame(0, $snapshot['total_errors']);
        $this->assertSame(0.0, $snapshot['error_rate']);
    }
}
