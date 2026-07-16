<?php

use PHPUnit\Framework\TestCase;

class GeneratorPaginationTest extends TestCase
{
    public function test_yields_all_rows_across_pages(): void
    {
        $data = [['a'], ['b'], ['c'], ['d'], ['e']];
        $fetch = function (int $page, int $perPage) use ($data): array {
            $slice = array_slice($data, ($page - 1) * $perPage, $perPage);
            return $slice;
        };

        $result = iterator_to_array(paginatedRows($fetch, perPage: 2), false);
        $this->assertSame($data, $result);
    }

    public function test_stops_on_empty_page(): void
    {
        $calls = 0;
        $fetch = function (int $page) use (&$calls): array {
            $calls++;
            return $page === 1 ? [['x'], ['y']] : [];
        };

        iterator_to_array(paginatedRows($fetch, perPage: 10), false);
        $this->assertSame(2, $calls); // page 1 (data) + page 2 (empty)
    }

    public function test_yields_nothing_for_empty_first_page(): void
    {
        $result = iterator_to_array(paginatedRows(fn() => [], perPage: 10), false);
        $this->assertSame([], $result);
    }

    public function test_passes_correct_arguments_to_fetch(): void
    {
        $received = [];
        $fetch = function (int $page, int $perPage) use (&$received): array {
            $received[] = [$page, $perPage];
            return $page < 3 ? [['row']] : [];
        };

        iterator_to_array(paginatedRows($fetch, perPage: 50), false);
        $this->assertSame([[1, 50], [2, 50], [3, 50]], $received);
    }

    public function test_returns_generator_instance(): void
    {
        $gen = paginatedRows(fn() => [], perPage: 10);
        $this->assertInstanceOf(\Generator::class, $gen);
    }
}
