<?php

use PHPUnit\Framework\TestCase;

class NullSafetyInProductionTest extends TestCase
{
    public function test_returns_value_when_attribute_exists(): void
    {
        $span = new Span(['user_id' => new Attribute('user_id', 42)]);
        $tx   = new Transaction('tx-001', $span);
        $this->assertSame(42, getCustomAttributeValue($tx, 'user_id'));
    }

    public function test_returns_null_when_span_is_null(): void
    {
        $tx = new Transaction('tx-002', null);
        $this->assertNull(getCustomAttributeValue($tx, 'user_id'));
    }

    public function test_returns_null_when_attribute_is_missing(): void
    {
        $span = new Span([]);
        $tx   = new Transaction('tx-003', $span);
        $this->assertNull(getCustomAttributeValue($tx, 'user_id'));
    }

    public function test_returns_string_attribute_value(): void
    {
        $span = new Span(['route' => new Attribute('route', '/api/users')]);
        $tx   = new Transaction('tx-004', $span);
        $this->assertSame('/api/users', getCustomAttributeValue($tx, 'route'));
    }

    public function test_returns_null_for_wrong_attribute_name(): void
    {
        $span = new Span(['host' => new Attribute('host', 'prod-1')]);
        $tx   = new Transaction('tx-005', $span);
        $this->assertNull(getCustomAttributeValue($tx, 'region'));
    }
}
