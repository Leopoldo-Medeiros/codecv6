<?php

enum Role: string
{
    case Viewer = 'viewer';
    case Editor = 'editor';
    case Admin  = 'admin';

    /**
     * Returns true if this role has permission to perform $action.
     * Known actions: read, write, delete, share, manage_users
     */
    public function can(string $action): bool
    {
        // TODO: implement the permission matrix using match
    }

    /**
     * Returns the Role for the given string, or throws \ValueError for unknown roles.
     */
    public static function fromString(string $role): self
    {
        // TODO: use self::from() — backed enums do this automatically,
        // but wrap it so callers get \ValueError with a clear message.
    }
}
