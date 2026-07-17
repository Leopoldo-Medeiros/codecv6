<?php

function sumRange(int $n): int
{
    if ($n === 1) {
        return 0;
    }

    return $n + sumRange($n - 1);
}
