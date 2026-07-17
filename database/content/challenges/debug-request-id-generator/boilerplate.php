<?php

class RequestIdGenerator
{
    private static int $counter = 0;

    public function next(): int
    {
        self::$counter++;

        return self::$counter;
    }
}
