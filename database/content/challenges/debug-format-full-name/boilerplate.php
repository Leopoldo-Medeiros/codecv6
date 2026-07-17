<?php

function formatFullName(string $firstName, string $lastName): string
{
    return sprintf('%s, %s', $firstName, $lastName);
}
