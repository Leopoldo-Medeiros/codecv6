<?php

function normalizeNames(array $names): array
{
    foreach ($names as &$name) {
        $name = ucfirst(strtolower(trim($name)));
    }

    // Used to log each normalized name here for debugging. The logging
    // call is gone, but the loop was left in — it looks like a no-op.
    foreach ($names as $name) {
    }

    return $names;
}
