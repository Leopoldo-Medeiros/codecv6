<?php

/**
 * Analyzes log lines and returns an incident report.
 *
 * Log format: "[TIMESTAMP] [LEVEL] [ENDPOINT] message"
 * Example: "[2026-05-06 12:00:01] [ERROR] [/api/users] Database connection timeout"
 *
 * @param  list<string> $logLines
 * @return array{severity: string, root_cause: string, affected_endpoints: list<string>, error_count: int}
 */
function analyzeLogs(array $logLines): array
{
    // TODO: parse log lines, identify patterns, return incident report
}
