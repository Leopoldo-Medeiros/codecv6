<?php

function excerpt(string $text, int $maxLength): string
{
    if (strlen($text) <= $maxLength) {
        return $text;
    }

    return substr($text, 0, $maxLength) . '...';
}
