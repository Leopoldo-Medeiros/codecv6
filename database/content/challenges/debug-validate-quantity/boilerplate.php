<?php

function validateQuantity(array $input): array
{
    $errors = [];

    if (empty($input['quantity'])) {
        $errors[] = 'Quantity is required.';
    }

    return $errors;
}
