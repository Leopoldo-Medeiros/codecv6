<?php

use PHPUnit\Framework\TestCase;

class PermissionMatrixTest extends TestCase
{
    public function test_viewer_can_read(): void
    {
        $this->assertTrue(Role::Viewer->can('read'));
    }

    public function test_viewer_cannot_write(): void
    {
        $this->assertFalse(Role::Viewer->can('write'));
    }

    public function test_viewer_cannot_delete(): void
    {
        $this->assertFalse(Role::Viewer->can('delete'));
    }

    public function test_editor_can_read_write_share(): void
    {
        $this->assertTrue(Role::Editor->can('read'));
        $this->assertTrue(Role::Editor->can('write'));
        $this->assertTrue(Role::Editor->can('share'));
    }

    public function test_editor_cannot_delete_or_manage_users(): void
    {
        $this->assertFalse(Role::Editor->can('delete'));
        $this->assertFalse(Role::Editor->can('manage_users'));
    }

    public function test_admin_can_all_actions(): void
    {
        foreach (['read', 'write', 'delete', 'share', 'manage_users'] as $action) {
            $this->assertTrue(Role::Admin->can($action), "Admin should be able to: $action");
        }
    }

    public function test_from_string_returns_correct_case(): void
    {
        $this->assertSame(Role::Admin, Role::fromString('admin'));
        $this->assertSame(Role::Editor, Role::fromString('editor'));
        $this->assertSame(Role::Viewer, Role::fromString('viewer'));
    }

    public function test_from_string_throws_for_unknown_role(): void
    {
        $this->expectException(\ValueError::class);
        Role::fromString('superuser');
    }
}
