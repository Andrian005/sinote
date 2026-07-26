<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Run test-specific migrations
        $this->artisan('migrate', [
            '--path' => 'tests/database/migrations',
            '--realpath' => true,
        ]);
    }
}
