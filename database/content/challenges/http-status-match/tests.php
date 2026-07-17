<?php

use PHPUnit\Framework\TestCase;

class HttpStatusMatchTest extends TestCase
{
    public function test_200_ok(): void
    {
        $this->assertSame('OK', statusLabel(200));
    }

    public function test_201_created(): void
    {
        $this->assertSame('Created', statusLabel(201));
    }

    public function test_400_bad_request(): void
    {
        $this->assertSame('Bad Request', statusLabel(400));
    }

    public function test_401_unauthorized(): void
    {
        $this->assertSame('Unauthorized', statusLabel(401));
    }

    public function test_403_forbidden(): void
    {
        $this->assertSame('Forbidden', statusLabel(403));
    }

    public function test_404_not_found(): void
    {
        $this->assertSame('Not Found', statusLabel(404));
    }

    public function test_422_unprocessable(): void
    {
        $this->assertSame('Unprocessable Entity', statusLabel(422));
    }

    public function test_500_internal_server_error(): void
    {
        $this->assertSame('Internal Server Error', statusLabel(500));
    }

    public function test_unknown_code_throws(): void
    {
        $this->expectException(\UnhandledMatchError::class);
        statusLabel(418);
    }
}
