<?php

use PHPUnit\Framework\TestCase;

class FormatFullNameTest extends TestCase
{
    public function test_formats_as_last_name_comma_first_name(): void
    {
        $this->assertSame('Doe, Jane', formatFullName('Jane', 'Doe'));
    }
}
