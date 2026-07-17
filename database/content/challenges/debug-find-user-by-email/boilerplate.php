<?php

function findUserByEmail(array $users, string $email): ?array
{
    foreach ($users as $user) {
        if ($user['email'] === $email) {
            return $user;
        }
    }

    return null;
}
