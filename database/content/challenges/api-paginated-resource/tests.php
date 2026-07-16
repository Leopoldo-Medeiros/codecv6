<?php

class PaginatedResourceTest extends \PHPUnit\Framework\TestCase
{
    public function test_wraps_items_in_data()
    {
        $r = paginate([['id' => 1], ['id' => 2]], 1, 2, 5);
        $this->assertCount(2, $r['data']);
    }

    public function test_computes_meta()
    {
        $r = paginate([], 2, 10, 45);
        $this->assertSame(2, $r['meta']['current_page']);
        $this->assertSame(10, $r['meta']['per_page']);
        $this->assertSame(45, $r['meta']['total']);
        $this->assertSame(5, $r['meta']['last_page']); // ceil(45 / 10)
    }

    public function test_last_page_never_below_one()
    {
        $r = paginate([], 1, 10, 0);
        $this->assertSame(1, $r['meta']['last_page']);
    }
}
