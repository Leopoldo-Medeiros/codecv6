<?php

class City
{
    public function __construct(public string $name) {}
}

class Address
{
    public function __construct(public ?City $city = null) {}
}

class User
{
    public function __construct(public ?Address $address = null) {}
}

function getCity(?User $user): ?string
{
    // TODO: return the city name using the null-safe operator,
    // or null if any part of the chain is absent.
}
