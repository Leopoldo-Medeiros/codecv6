<?php

/**
 * Validate a create-order payload. Return an array of error strings
 * (an empty array means the payload is valid). Rules:
 *
 *   product_id  required, integer, greater than 0
 *   quantity    required, integer, between 1 and 100 (inclusive)
 *   currency    required, one of: eur, brl
 */
function validateOrder(array $input): array
{
    $errors = [];
    // TODO: apply the three rules above, adding one message per failure.
    return $errors;
}
