<?php

use PHPUnit\Framework\TestCase;

class NormalizeNamesTest extends TestCase
{
    public function test_trims_and_fixes_the_casing_of_every_name(): void
    {
        $result = normalizeNames(['ALICE', ' bob ', 'CAROL']);

        $this->assertSame(['Alice', 'Bob', 'Carol'], $result);
    }

    public function test_does_not_duplicate_the_last_name_over_the_one_before_it(): void
    {
        $result = normalizeNames(['one', 'two', 'three', 'four']);

        $this->assertSame(['One', 'Two', 'Three', 'Four'], $result);
    }
}
