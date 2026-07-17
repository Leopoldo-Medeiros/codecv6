<?php

use PHPUnit\Framework\TestCase;

class GitMergeTest extends TestCase
{
    private array $ours = [
        'db_host' => 'localhost',
        'db_port' => 3306,
        'cache_ttl' => 3600,
        'debug' => false,
    ];

    private array $theirs = [
        'db_host' => 'prod-db.internal',
        'db_port' => 5432,
        'api_key' => 'secret123',
        'debug' => true,
    ];

    public function test_ours_strategy_keeps_our_values(): void
    {
        $result = mergeConfigs($this->ours, $this->theirs, 'ours');

        $this->assertSame('localhost', $result['db_host']);
        $this->assertSame(3306, $result['db_port']);
    }

    public function test_ours_strategy_fills_gaps(): void
    {
        $result = mergeConfigs($this->ours, $this->theirs, 'ours');

        $this->assertSame('secret123', $result['api_key']);
    }

    public function test_theirs_strategy_keeps_their_values(): void
    {
        $result = mergeConfigs($this->ours, $this->theirs, 'theirs');

        $this->assertSame('prod-db.internal', $result['db_host']);
        $this->assertSame(5432, $result['db_port']);
    }

    public function test_combine_uses_ours_for_conflicts(): void
    {
        $result = mergeConfigs($this->ours, $this->theirs, 'combine');

        $this->assertSame('localhost', $result['db_host']);
        $this->assertSame('secret123', $result['api_key']);
        $this->assertSame(3600, $result['cache_ttl']);
    }

    public function test_empty_arrays(): void
    {
        $this->assertSame([], mergeConfigs([], [], 'ours'));
        $this->assertSame(['a' => 1], mergeConfigs([], ['a' => 1], 'theirs'));
    }
}
