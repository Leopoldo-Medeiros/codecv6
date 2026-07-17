<?php

use PHPUnit\Framework\TestCase;

class NullSafeTest extends TestCase
{
    public function test_returns_city_name_when_full_chain_exists(): void
    {
        $user = new User(new Address(new City('Dublin')));
        $this->assertSame('Dublin', getCity($user));
    }

    public function test_returns_null_when_user_is_null(): void
    {
        $this->assertNull(getCity(null));
    }

    public function test_returns_null_when_address_is_null(): void
    {
        $this->assertNull(getCity(new User(null)));
    }

    public function test_returns_null_when_city_is_null(): void
    {
        $this->assertNull(getCity(new User(new Address(null))));
    }
}
