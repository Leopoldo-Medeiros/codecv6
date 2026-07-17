<?php

function hasPermission(array $permissions, string $permission): bool
{
    return array_search($permission, $permissions) ? true : false;
}
