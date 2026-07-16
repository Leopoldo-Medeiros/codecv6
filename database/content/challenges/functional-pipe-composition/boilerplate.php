<?php

/**
 * Returns a new callable that applies $fns left-to-right.
 * pipe(f, g, h)($x) === h(g(f($x)))
 * pipe()($x)        === $x   (identity)
 */
function pipe(callable ...$fns): callable
{
    // TODO: use array_reduce to compose the functions.
}
