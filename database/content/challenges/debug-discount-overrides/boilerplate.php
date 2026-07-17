<?php

function applyDiscountOverrides(array $baseDiscounts, array $overrides): array
{
    return array_merge($baseDiscounts, $overrides);
}
