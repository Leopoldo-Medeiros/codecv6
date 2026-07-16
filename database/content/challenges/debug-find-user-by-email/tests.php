<?php

use PHPUnit\Framework\TestCase;

class FindUserByEmailTest extends TestCase
{
    public function test_finds_user_regardless_of_email_casing(): void
    {
        $users = [['name' => 'John', 'email' => 'john@example.com']];

        $result = findUserByEmail($users, 'John@Example.com');

        $this->assertNotNull($result);
        $this->assertSame('John', $result['name']);
    }

    public function test_returns_null_when_no_match(): void
    {
        $users = [['name' => 'John', 'email' => 'john@example.com']];

        $this->assertNull(findUserByEmail($users, 'jane@example.com'));
    }
}
