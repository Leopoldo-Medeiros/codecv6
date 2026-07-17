<?php

class MetricsCollector
{
    private int $totalRequests = 0;
    private int $totalErrors = 0;
    private float $totalDuration = 0;

    public function startRequest(): void
    {
        // TODO: record request start time
    }

    public function endRequest(int $statusCode): void
    {
        // TODO: calculate duration, increment counters
    }

    /**
     * @return array{total_requests: int, total_errors: int, error_rate: float, avg_duration_ms: float}
     */
    public function snapshot(): array
    {
        return [
            'total_requests' => 0,
            'total_errors' => 0,
            'error_rate' => 0.0,
            'avg_duration_ms' => 0.0,
        ];
    }
}
