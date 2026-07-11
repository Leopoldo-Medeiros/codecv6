<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Http;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // No test may ever reach a live API (Judge0, Gemini, Jina, Stripe).
        // Any HTTP call not covered by Http::fake() throws immediately
        // instead of silently going out over the network.
        Http::preventStrayRequests();
    }
}
