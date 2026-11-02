<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\ParallelTesting;
use Illuminate\Support\Facades\Storage;
use LogicException;

abstract class TestCase extends BaseTestCase
{
    public function createApplication()
    {
        $app = parent::createApplication();

        if (! $app->environment('testing')) {
            throw new LogicException('Tests must run in the testing environment.');
        }

        return $app;
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
        ParallelTesting::resolveTokenUsing(fn () => $_SERVER['TEST_TOKEN'] ?? getmypid());
        Http::preventStrayRequests();
        Mail::fake();
        Storage::fake('local');
        Storage::fake('public');
    }
}
