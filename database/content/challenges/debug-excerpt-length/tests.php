<?php

use PHPUnit\Framework\TestCase;

class ExcerptTest extends TestCase
{
    public function test_short_text_is_returned_unchanged(): void
    {
        $this->assertSame('Hello', excerpt('Hello', 10));
    }

    public function test_long_text_is_truncated_to_max_length_including_ellipsis(): void
    {
        $result = excerpt('This is a fairly long sentence that needs cutting', 20);

        $this->assertSame(20, strlen($result));
        $this->assertStringContainsString('...', $result);
    }
}
