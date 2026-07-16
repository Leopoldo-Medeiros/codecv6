<?php

use PHPUnit\Framework\TestCase;

class HasPermissionTest extends TestCase
{
    public function test_permission_found_at_the_first_index_is_true(): void
    {
        $permissions = ['edit-posts', 'delete-posts'];

        $this->assertTrue(hasPermission($permissions, 'edit-posts'));
    }

    public function test_permission_found_later_in_the_list_is_true(): void
    {
        $permissions = ['view-posts', 'edit-posts'];

        $this->assertTrue(hasPermission($permissions, 'edit-posts'));
    }

    public function test_permission_not_in_the_list_is_false(): void
    {
        $permissions = ['view-posts'];

        $this->assertFalse(hasPermission($permissions, 'delete-posts'));
    }
}
