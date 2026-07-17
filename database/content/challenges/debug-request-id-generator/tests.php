<?php

use PHPUnit\Framework\TestCase;

class RequestIdGeneratorTest extends TestCase
{
    public function test_first_instance_starts_at_one(): void
    {
        $gen = new RequestIdGenerator();

        $this->assertSame(1, $gen->next());
        $this->assertSame(2, $gen->next());
    }

    public function test_a_new_instance_starts_over_at_one(): void
    {
        $first = new RequestIdGenerator();
        $first->next();
        $first->next();

        $second = new RequestIdGenerator();

        $this->assertSame(1, $second->next());
    }
}
