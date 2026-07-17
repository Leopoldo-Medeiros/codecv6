<?php

use PHPUnit\Framework\TestCase;

class FunctionalPipeTest extends TestCase
{
    public function test_single_function_is_applied(): void
    {
        $double = fn(int $n) => $n * 2;
        $this->assertSame(10, pipe($double)(5));
    }

    public function test_two_functions_applied_left_to_right(): void
    {
        $addOne  = fn(int $n) => $n + 1;
        $double  = fn(int $n) => $n * 2;
        // pipe(addOne, double)(3) = double(addOne(3)) = double(4) = 8
        $this->assertSame(8, pipe($addOne, $double)(3));
    }

    public function test_three_functions_applied_in_order(): void
    {
        $lower  = fn(string $s) => strtolower($s);
        $trim   = fn(string $s) => trim($s);
        $slug   = fn(string $s) => str_replace(' ', '-', $s);

        $this->assertSame('hello-world', pipe($lower, $trim, $slug)('  Hello World  '));
    }

    public function test_empty_pipe_returns_identity(): void
    {
        $this->assertSame(42, pipe()(42));
        $this->assertSame('foo', pipe()('foo'));
    }

    public function test_works_with_builtin_callables(): void
    {
        $this->assertSame(5, pipe('strlen')('hello'));
    }
}
